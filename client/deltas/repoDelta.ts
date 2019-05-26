import Kefir, { Stream, Property, Observable } from 'kefir';
import { ofType } from 'brookjs';
import * as t from 'io-ts';
import {
  editorUpdateClick,
  ajaxStarted,
  ajaxFailed,
  ajaxFinished,
  repoSaveSucceeded
} from '../actions';
import { RootAction, toggle } from '../util';
import {
  RepoState,
  GlobalsState,
  EditorState,
  EditorInstance
} from '../reducers';
import { Nullable } from 'typescript-nullable';
import { AjaxService } from '../ajax';

type RepoDeltaState = {
  repo: RepoState;
  globals: GlobalsState;
  editor: EditorState;
};

type RepoDeltaServices = {
  ajax$: AjaxService;
};

const repoProps = ({ editor }: RepoDeltaState) => ({
  description: editor.description,
  status: editor.status,
  password: editor.password,
  sync: editor.sync
});

const blobProps = (editor: EditorInstance) => ({
  filename: editor.filename,
  code: editor.code,
  language: editor.language
});

const makeBody = (state: RepoDeltaState) =>
  JSON.stringify({
    ...repoProps(state),
    blobs: state.editor.instances.map(instance => {
      const blob = blobProps(instance) as {
        ID?: number;
      };

      if (Nullable.maybe(false, key => key.includes('new'), instance.key)) {
        // If key includes new, then we know it exists.
        blob.ID = parseInt(instance.key!, 10);
      }

      return blob;
    })
  });

export const apiLanguage = t.type({
  ID: t.number,
  display_name: t.string,
  slug: t.string
});

// @TODO(mAAdhaTTah) dedupe from searchDelta
export const apiBlob = t.type({
  filename: t.string,
  code: t.string,
  language: apiLanguage,
  ID: t.number,
  size: t.number,
  raw_url: t.string,
  edit_url: t.string
});

export const apiRepo = t.type({
  ID: t.number,
  description: t.string,
  status: t.string,
  password: t.string,
  gist_id: t.string,
  gist_url: t.union([t.string, t.null]),
  sync: toggle,
  blobs: t.array(apiBlob),
  rest_url: t.string,
  commits_url: t.string,
  html_url: t.string,
  created_at: t.string,
  updated_at: t.string
});

export type ApiRepo = t.TypeOf<typeof apiRepo>;

export const repoDelta = ({ ajax$ }: RepoDeltaServices) => (
  action$: Stream<RootAction, never>,
  state$: Property<RepoDeltaState, never>
): Observable<RootAction, never> =>
  state$
    .sampledBy(action$.thru(ofType(editorUpdateClick)))
    .flatMapLatest(state =>
      Nullable.maybe(
        Kefir.never(),
        repo =>
          Kefir.concat<RootAction, never>([
            Kefir.constant(ajaxStarted()),
            ajax$(repo.rest_url, {
              method: 'PUT',
              body: makeBody(state),
              credentials: 'include',
              headers: {
                'X-WP-Nonce': state.globals.nonce,
                'Content-Type': 'application/json'
              }
            })
              .flatMap(response => response.json())
              .flatMap(response =>
                apiRepo
                  .validate(response, [])
                  .fold<Observable<t.TypeOf<typeof apiRepo>, Error>>(
                    () =>
                      Kefir.constantError(
                        new Error('API response was invalid')
                      ),
                    Kefir.constant
                  )
              )
              .flatten(response => [
                ajaxFinished(),
                repoSaveSucceeded(response)
              ])
              .flatMapErrors(err => Kefir.constant(ajaxFailed(err)))
          ]),
        state.repo
      )
    );
