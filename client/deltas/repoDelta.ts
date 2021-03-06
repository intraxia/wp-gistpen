import Kefir, { Stream, Property, Observable } from 'kefir';
import { ofType } from 'brookjs';
import { Nullable } from 'typescript-nullable';
import { ajax$ } from 'kefir-ajax';
import {
  editorUpdateClick,
  ajaxStarted,
  ajaxFailed,
  ajaxFinished,
  repoSaveSucceeded,
} from '../actions';
import { RootAction } from '../RootAction';
import { RepoState, EditorState, EditorInstance } from '../reducers';
import { ValidationError, JsonError } from '../api';
import { GlobalsState } from '../globals';
import { ApiRepo } from '../snippet';

type RepoDeltaState = {
  repo: RepoState;
  globals: GlobalsState;
  editor: EditorState;
};

type RepoDeltaServices = {
  ajax$: typeof ajax$;
};

const repoProps = ({ editor }: RepoDeltaState) => ({
  description: editor.description,
  status: editor.status,
  password: editor.password,
  sync: editor.sync,
});

const blobProps = (editor: EditorInstance) => ({
  filename: editor.filename,
  code: editor.code,
  language: editor.language,
});

const makeBody = (state: RepoDeltaState) =>
  JSON.stringify({
    ...repoProps(state),
    blobs: state.editor.instances.map(instance => {
      const blob = blobProps(instance) as {
        ID?: number;
      };

      if (!instance.key.includes('new')) {
        // If key includes new, then we know it exists.
        blob.ID = parseInt(instance.key, 10);
      }

      return blob;
    }),
  });

export const repoDelta = ({ ajax$ }: RepoDeltaServices) => (
  action$: Stream<RootAction, never>,
  state$: Property<RepoDeltaState, never>,
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
                'Content-Type': 'application/json',
              },
            })
              .flatMap(response =>
                response.json().mapErrors(err => new JsonError(err)),
              )
              .flatMap(response =>
                ApiRepo.validate(response, []).fold<
                  Observable<ApiRepo, ValidationError>
                >(
                  errs => Kefir.constantError(new ValidationError(errs)),
                  Kefir.constant,
                ),
              )
              .flatten(response => [
                ajaxFinished(),
                repoSaveSucceeded(response),
              ])
              .flatMapErrors(err => Kefir.constant(ajaxFailed(err))),
          ]),
        state.repo,
      ),
    );
