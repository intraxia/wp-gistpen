import { Delta } from 'brookjs-types';
import Kefir, { Observable } from 'kefir';
import { ofType } from 'brookjs-flow';
import * as t from 'io-ts';
import { ajax$ } from '../../../../ajax';
import { ApiRepo, validationErrorsToString } from '../../../../api';
import { saveNewBtnClick, createRepo } from './actions';
import { State, Action } from './state';

const onError = (errs: t.Errors) =>
  Kefir.constantError(new TypeError(validationErrorsToString(errs)));
const onSuccess = (response: ApiRepo) => {
  if (response.blobs[0] == null) {
    return Kefir.constantError(new TypeError('No blob returned in response.'));
  }

  return Kefir.constant(createRepo.success(response));
};

const delta: Delta<Action, State> = (action$, state$) =>
  state$
    .sampledBy(action$.thru(ofType(saveNewBtnClick)))
    .flatMap(state =>
      ajax$(`${state.globals.root}repos`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'X-WP-Nonce': state.globals.nonce,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          description: state.description,
          blobs: [{ filename: 'draft', code: '' }]
        })
      })
    )
    .flatMap(response => response.json())
    .flatMap(body =>
      ApiRepo.validate(body, []).fold<Observable<Action, TypeError>>(
        onError,
        onSuccess
      )
    )
    .flatMapErrors(error => Kefir.constant(createRepo.failure(error)));

export default delta;
