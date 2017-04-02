// @flow
import type { Emitter, Observable } from 'kefir';
import type { Action, Delta, RouterDeltaOptions } from '../type';
import R from 'ramda';
import { stream } from 'kefir';
import hash from 'sheet-router/hash';

/**
 * Create a routerDelta function.
 *
 * @param {sheetRouter} router - Sheet router.
 * @returns {Function} Router delta creating function.
 */
export default function routerDelta({ router } : RouterDeltaOptions) : Delta<Action, void> {
    const getAction = R.pipe(
        R.replace('#', '/'),
        router
    );

    /**
     * Creates the router stream.
     *
     * @returns {Observable<T, S>} Stream of routing actions.
     */
    return () : Observable<Action> => {
        return stream((emitter : Emitter<Action, void>) => {
            // Emit current route.
            emitter.value(getAction(window.location.hash));
            // Listen for hash changes.
            hash(R.pipe(getAction, emitter.value));
        });
    };
}
