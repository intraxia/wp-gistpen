// @flow
/* global tinymce */
import type { Emitter, Observable } from 'kefir';
import type { TinyMCEAction as Action, TinyMCEEditor as Editor } from '../type';
import R from 'ramda';
import { merge, stream } from 'kefir';
import { tinymceButtonClickAction, tinymcePopupInsertClickAction, tinymcePopupCloseClickAction,
    TINYMCE_BUTTON_CLICK, TINYMCE_POPUP_CLOSE_CLICK } from '../action';
import template from '../component/search/index.hbs';

const createTinyMCEPlugin = () : Observable<Editor> => stream((emitter : Emitter<Editor, void>) => {
    tinymce.PluginManager.add('wp_gistpen', emitter.value);
});

const createTinyMCEButton = (editor : Editor) : Observable<Action> => stream((emitter : Emitter<Action, void>) => {
    // Bind command to stream.
    editor.addCommand('wpgp_insert', R.pipe(tinymceButtonClickAction, emitter.value));

    // Add the Insert Gistpen button
    editor.addButton('wp_gistpen', {
        icon: 'icons dashicons-editor-code',
        tooltip: 'Insert Gistpen',
        cmd: 'wpgp_insert'
    });
});

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
        R.equals(TINYMCE_POPUP_CLOSE_CLICK)
    )));

const mergeTinyMCEButtonAndPopup = R.curry((actions$ : Observable<Action>, editor : Editor) : Observable<Action> => merge([
    createTinyMCEButton(editor),
    actions$.filter(
        R.pipe(R.prop('type'), R.equals(TINYMCE_BUTTON_CLICK))
    )
        .flatMapLatest(() : Observable<Action> => createTinyMCEWindow(actions$, editor))
]));

export default (actions$ : Observable<Action>) : Observable<Action> =>
    createTinyMCEPlugin().flatMapLatest(mergeTinyMCEButtonAndPopup(actions$));
