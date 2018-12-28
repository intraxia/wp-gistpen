// @flow
import type { Emitter, Observable } from 'kefir';
import type { Action, RouterDeltaOptions, RouteChangeAction } from '../types';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import href from 'sheet-router/href';
import history from 'sheet-router/history';
import { ROUTE_CHANGE } from '../actions';
import { getRoute, getUrl, parseQueryString } from '../selectors';

type HrefTarget = {
    search: {
        [key: string]: string
    } | string,
    href: string
};

/**
 * Create a hashRouterDelta function.
 *
 * @param {sheetRouter} router - Sheet router.
 * @param {string} param - Query param to use.
 * @returns {Function} Router delta creating function.
 */
export default function routerDelta({ router, param }: RouterDeltaOptions) {
    /**
     * Creates the router stream.
     *
     * @param {Observable<Action>} actions$ - Stream of actions from the app.
     * @returns {Observable<T, S>} Stream of routing actions.
     */
    return (actions$: Observable<Action>): Observable<Action> => {
        const initial$ = Kefir.later(0, router(getRoute(window.location.search, param)));

        const pushState$ = actions$.thru(ofType(ROUTE_CHANGE)).flatMap(({ payload }: RouteChangeAction) => Kefir.stream((emitter: Emitter<void, void>) => {
            const dest = getUrl(param, payload);

            if (dest !== (location.pathname + location.search)) {
                global.history.pushState({}, '', dest);
            }

            emitter.end();
        }));

        const href$ = Kefir.stream((emitter: Emitter<Action, empty>) => {
            const emit = ({ search, href }: HrefTarget) => {
                if (typeof search === 'string') {
                    search = parseQueryString(search);
                }

                if (search[param]) {
                    emitter.value(router(`/${search[param]}`));
                } else {
                    location.href = href;
                    emitter.end();
                }
            };

            href(emit);
            history(emit);
        });

        return Kefir.merge([
            initial$,
            pushState$,
            href$
        ]);
    };
}
