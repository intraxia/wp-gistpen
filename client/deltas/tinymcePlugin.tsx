import Kefir, { Emitter, Observable, Property, Stream } from 'kefir';
import { ofType } from 'brookjs-flow';
import React from 'react';
import { RootJunction } from 'brookjs-silt';
import ReactDOM from 'react-dom';
import * as tmce from 'tinymce';
import { SearchPopup } from '../components';
import {
  tinymceButtonClick,
  tinymcePopupInsertClick,
  tinymcePopupCloseClick,
} from '../actions';
import { RootAction } from '../util';
import { SearchState, AjaxState } from '../reducers';

declare global {
  interface Window {
    tinymce: typeof tmce;
  }
}

type TinyMCEPluginState = {
  ajax: AjaxState;
  search: SearchState;
};

const { tinymce } = window;

const createTinyMCEPlugin = (): Observable<tmce.Editor, never> =>
  Kefir.stream((emitter: Emitter<tmce.Editor, never>) => {
    tinymce.PluginManager.add('wp_gistpen', emitter.value);
  });

const createTinyMCEButton = (
  actions$: Stream<RootAction, never>,
  state$: Property<TinyMCEPluginState, never>,
  editor: tmce.Editor,
): Observable<RootAction, never> =>
  Kefir.merge<RootAction, never>([
    Kefir.stream((emitter: Emitter<RootAction, never>) => {
      // Bind command to stream.
      editor.addCommand('wpgp_insert', () =>
        emitter.value(tinymceButtonClick()),
      );

      // Add the Insert Gistpen button
      editor.addButton('wp_gistpen', {
        icon: 'icons dashicons-editor-code',
        tooltip: 'Insert Gistpen',
        cmd: 'wpgp_insert',
      });
    }),
    state$
      .sampledBy(actions$.thru(ofType(tinymcePopupInsertClick)))
      .flatMap(state =>
        Kefir.stream((emitter: Emitter<RootAction, never>) => {
          if (state.search.selection != null) {
            editor.insertContent(
              '[gistpen id="' + state.search.selection + '"]',
            );
          }

          emitter.end();
        }),
      ),
  ]);

const emitTinyMCEWindow = (editor: tmce.Editor) => (
  emitter: Emitter<RootAction, Element>,
) => {
  const id = `wpgp-tinymce-popup-container`;
  const e = editor.windowManager.open(
    {
      // Modal settings
      title: 'Insert Gistpen',
      width: 400,
      // minus head and foot of dialog box
      height: 300 - 36 - 50,
      inline: 1,
      id,
      buttons: [
        {
          text: 'Insert',
          id: 'wpgp-popup-insert',
          onclick: () => emitter.value(tinymcePopupInsertClick()),
        },
        {
          text: 'Cancel',
          id: 'wpgp-popup-cancel',
          onclick: () => emitter.value(tinymcePopupCloseClick()),
        },
      ],
    },
    {},
  ) as any;

  const $el = jQuery('<div class="app"></div>');

  e.$el.find(`#${id}-body`).append($el);

  emitter.error($el[0]);

  return () => e.close();
};

const createTinyMCEWindow = (
  actions$: Observable<RootAction, never>,
  state$: Observable<TinyMCEPluginState, never>,
  editor: tmce.Editor,
): Observable<RootAction, never> =>
  Kefir.stream(emitTinyMCEWindow(editor))
    // This is kind of abusive, b/c it's not an "error", but it's another channel to use...
    .flatMapErrors(el =>
      Kefir.stream<RootAction, never>(emitter => {
        const root$ = (action$: Observable<RootAction, Error>) =>
          action$.observe(emitter.value);
        const sub = state$.observe(state => {
          ReactDOM.render(
            <RootJunction<RootAction> root$={root$}>
              <SearchPopup
                loading={state.ajax.running}
                term={state.search.term}
                results={state.search.results.map(blob => ({
                  id: blob.ID,
                  filename: blob.filename,
                }))}
              />
            </RootJunction>,
            el,
          );
        });

        return () => {
          sub.unsubscribe();
          ReactDOM.unmountComponentAtNode(el);
        };
      }),
    )
    .takeUntilBy(
      actions$.thru(ofType(tinymcePopupCloseClick, tinymcePopupInsertClick)),
    );

const mergeTinyMCEButtonAndPopup = (
  actions$: Observable<RootAction, never>,
  state$: Observable<TinyMCEPluginState, never>,
) => (editor: tmce.Editor): Observable<RootAction, never> =>
  Kefir.merge([
    createTinyMCEButton(actions$, state$, editor),
    actions$
      .thru(ofType(tinymceButtonClick))
      .flatMapLatest(() => createTinyMCEWindow(actions$, state$, editor)),
  ]);

export const tinymcePluginDelta = (
  actions$: Observable<RootAction, never>,
  state$: Observable<TinyMCEPluginState, never>,
): Observable<RootAction, never> =>
  createTinyMCEPlugin().flatMapLatest(
    mergeTinyMCEButtonAndPopup(actions$, state$),
  );
