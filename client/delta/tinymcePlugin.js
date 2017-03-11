// @flow
/* global tinymce */
import type { Emitter, Observable } from 'kefir';
import type { TinyMCEAction as Action, TinyMCEEditor as Editor, TinyMCEState } from '../type';
import R from 'ramda';
import { merge, stream } from 'kefir';
import { tinymceButtonClickAction, tinymcePopupInsertClickAction, tinymcePopupCloseClickAction,
    TINYMCE_BUTTON_CLICK, TINYMCE_POPUP_CLOSE_CLICK, TINYMCE_POPUP_INSERT_CLICK } from '../action';
import template from '../component/search/index.hbs';

const createTinyMCEPlugin = () : Observable<Editor> => stream((emitter : Emitter<Editor, void>) => {
    tinymce.PluginManager.add('wp_gistpen', emitter.value);
});

const createTinyMCEButton = (actions$ : Observable<Action>, state$ : Observable<TinyMCEState>, editor : Editor) : Observable<Action> => merge([
    stream((emitter : Emitter<Action, void>) => {
        // Bind command to stream.
        editor.addCommand('wpgp_insert', R.pipe(tinymceButtonClickAction, emitter.value));

        // Add the Insert Gistpen button
        editor.addButton('wp_gistpen', {
            icon: 'icons dashicons-editor-code',
            tooltip: 'Insert Gistpen',
            cmd: 'wpgp_insert'
        });
    }),
    state$.sampledBy(actions$.filter(R.pipe(
        R.prop('type'),
        R.equals(TINYMCE_POPUP_INSERT_CLICK)
    )))
        .flatMap((state : TinyMCEState) => stream((emitter : Emitter<Action, void>) => {
            if (state.search.selection) {
                editor.insertContent('[gistpen id="' + state.search.selection + '"]');
            }

            emitter.end();
        }))
]);

const emitTinyMCEWindow = R.curry((editor : Editor, emitter : Emitter<Action, void>) : Disposer => {
    const id = `wpgp-tinymce-popup-container`;
    const e = editor.windowManager.open({
        // Modal settings
        title: 'Insert Gistpen',
        width: 400,
        // minus head and foot of dialog box
        height: (300 - 36 - 50),
        inline: 1,
        id,
        buttons: [
            {
                text: 'Insert',
                id: 'wpgp-popup-insert',
                onclick: R.pipe(tinymcePopupInsertClickAction, emitter.value)
            },
            {
                text: 'Cancel',
                id: 'wpgp-popup-cancel',
                onclick: R.pipe(tinymcePopupCloseClickAction, emitter.value)
            }
        ]
    });

    e.$el.find(`#${id}-body`).append(template({}));

    // void the return value for tcomb
    return () : void => void e.close();
});

const createTinyMCEWindow = (actions$ : Observable<Action>, editor : Editor) : Observable<Action> => stream(emitTinyMCEWindow(editor))
    .takeUntilBy(actions$.filter(R.pipe(
        R.prop('type'),
        R.converge(R.or, [
            R.equals(TINYMCE_POPUP_CLOSE_CLICK),
            R.equals(TINYMCE_POPUP_INSERT_CLICK)
        ])
    )));

const mergeTinyMCEButtonAndPopup = R.curry((actions$ : Observable<Action>, state$ : Observable<TinyMCEState>, editor : Editor) : Observable<Action> => merge([
    createTinyMCEButton(actions$, state$, editor),
    actions$.filter(
        R.pipe(R.prop('type'), R.equals(TINYMCE_BUTTON_CLICK))
    )
        .flatMapLatest(() : Observable<Action> => createTinyMCEWindow(actions$, editor))
]));

export default (actions$ : Observable<Action>, state$ : Observable<TinyMCEState>) : Observable<Action> =>
    createTinyMCEPlugin().flatMapLatest(mergeTinyMCEButtonAndPopup(actions$, state$));
