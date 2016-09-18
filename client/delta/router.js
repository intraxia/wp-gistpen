import R from 'ramda';
import { stream } from 'kefir';
import hash from 'sheet-router/hash';

/**
 * Create a routerDelta function.
 *
 * @param {sheetRouter} router - Sheet router.
 * @returns {Function} Router delta creating function.
 */
export default function createRouterDelta(router) {
    const getAction = R.pipe(
        R.replace('#', '/'),
        router
    );

    /**
     * Creates the router stream.
     *
     * @returns {Observable<T, S>} Stream of routing actions.
     */
    return function routerDelta() {
        return stream(emitter => {
            // Emit current route.
            emitter.value(getAction(window.location.hash));
            // Listen for hash changes.
            hash(R.pipe(getAction, emitter.value));
        });
    };
}
