import { Observable, stream } from 'kefir';
import { GlobalsState } from '../reducers';
import { RootAction } from '../util';

declare let __webpack_public_path__: string;

type WebpackDeltaState = {
  globals: GlobalsState;
};

export const webpackDelta = (
  actions$: Observable<RootAction, never>,
  state$: Observable<WebpackDeltaState, never>
): Observable<never, never> =>
  state$.take(1).flatMap(props =>
    stream(emitter => {
      // eslint-disable-next-line camelcase
      __webpack_public_path__ = props.globals.url + 'assets/js/';

      emitter.end();
    })
  );
