import { Observable, stream } from 'kefir';
import { GlobalsState } from '../globals';
import { RootAction } from '../RootAction';

type WebpackDeltaState = {
  globals: GlobalsState;
};

export const webpackDelta = (
  actions$: Observable<RootAction, never>,
  state$: Observable<WebpackDeltaState, never>,
): Observable<never, never> =>
  state$.take(1).flatMap(props =>
    stream(emitter => {
      window.__webpack_public_path__ = props.globals.url + 'resources/assets/';

      emitter.end();
    }),
  );
