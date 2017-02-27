// @flow
/* global tinymce */
import type { Emitter, Observable } from 'kefir';
import type { TinyMCEAction as Action, TinyMCEEditor as Editor, Disposer } from '../type';
import R from 'ramda';
import { merge, stream } from 'kefir';
import { tinymceButtonClickAction, tinymcePopupInsertClickAction, tinymcePopupCloseClickAction,
    TINYMCE_BUTTON_CLICK } from '../action';

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
    const e = editor.windowManager.open({
        // Modal settings
        title: 'Insert Gistpen',
        width: 400,
        // minus head and foot of dialog box
        height: (300 - 36 - 50),
        inline: 1,
        id: 'wpgp-insert-dialog',
        buttons: [
            {
                text: 'Insert',
                id: 'wp-gistpen-button-insert',
                class: 'insert',
                onclick: R.pipe(tinymcePopupInsertClickAction, emitter.value)
            },
            {
                text: 'Cancel',
                id: 'wp-gistpen-button-cancel',
                onclick: R.pipe(tinymcePopupCloseClickAction, emitter.value)
            }
        ]
    });

    return () : void => e.close();
});

const createTinyMCEWindow = (editor : Editor) : Observable<Action> => stream(emitTinyMCEWindow(editor));

const mergeTinyMCEButtonAndPopup = R.curry((actions$ : Observable<Action>, editor : Editor) : Observable<Action> => merge([
    createTinyMCEButton(editor),
    actions$.filter(
        R.pipe(R.prop('type'), R.equals(TINYMCE_BUTTON_CLICK))
    )
        .flatMap(() : Observable<Action> => createTinyMCEWindow(editor))
]));

export default (actions$ : Observable<Action>) : Observable<Action> =>
    createTinyMCEPlugin().flatMapLatest(mergeTinyMCEButtonAndPopup(actions$));
