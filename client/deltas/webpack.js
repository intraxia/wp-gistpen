// @flow
import type { Emitter, Observable } from 'kefir';
import type { Action, HasGlobalsState } from '../types';
import { stream } from 'kefir';

/**
 * Updates the webpack configuration settings based on the context
 * of the application.
 *
 * @param {Observable<Action>} actions$ - Observable of Actions.
 * @param {Observable<SettingsState>} state$ - Observable of props.
 * @returns {Stream<U, V>} Observable of WebPack side effects.
 */
export default function webpackDelta(actions$: Observable<Action>, state$: Observable<HasGlobalsState>): Observable<void> {
    return state$.take(1).flatMap((props: HasGlobalsState): Observable<void> => stream((emitter: Emitter<void, void>) => {
        // eslint-disable-next-line camelcase
        __webpack_public_path__ = props.globals.url + 'assets/js/';

        emitter.end();
    }));
}
