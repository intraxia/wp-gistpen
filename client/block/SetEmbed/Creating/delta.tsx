import Kefir from 'kefir';
import { Delta, sampleByAction, ofType } from 'brookjs';
import { ajax$ } from 'kefir-ajax';
import { RootAction } from '../../../util';
import { createRepo, appendBlob } from '../../actions';
import { foldResponse } from '../../../api';
import { ApiRepo, ApiBlob } from '../../../snippet';
import { State, NewRepoState, ChooseRepoState } from './state';

const isChooseState = (state: State): state is ChooseRepoState =>
  state.status === 'choose-existing';

export const rootDelta: Delta<RootAction, State> = (action$, state$) => {
  const createRepo$ = state$
    .thru(sampleByAction(action$, createRepo.request))
    .filter((state): state is NewRepoState => state.status === 'create-new')
    .flatMapFirst(state =>
      ajax$(`${state.globals.root}repos`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': state.globals.nonce,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          description: state.description,
          blobs: [
            {
              filename: state.filename,
            },
          ],
        }),
      }).thru(foldResponse(ApiRepo, createRepo.success, createRepo.failure)),
    );

  const appendBlob$ = state$
    .filter(isChooseState)
    .thru(sampleByAction(action$, appendBlob.request))
    .zip(
      action$.thru(ofType(appendBlob.request)),
      (state, action) => [state, action] as const,
    )
    .flatMapFirst(([state, action]) =>
      ajax$(`${state.globals.root}repos/${action.payload}/blobs`, {
        method: 'POST',
        headers: {
          'X-WP-Nonce': state.globals.nonce,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          filename: state.filename,
        }),
      }).thru(
        foldResponse(
          ApiBlob,
          blob => appendBlob.success({ repoId: action.payload, blob }),
          appendBlob.failure,
        ),
      ),
    );

  // @TODO(mAAdhaTTah) remove cast when `thru` definition fixed
  return Kefir.merge<RootAction, never>([
    createRepo$ as any,
    appendBlob$ as any,
  ]);
};
