import Kefir, { Observable } from 'kefir';
import { ofType } from 'brookjs';
import href from 'sheet-router/href';
import hist from 'sheet-router/history';
import { routeChange } from '../actions';
import { getRoute, getUrl, parseQueryString } from '../selectors';
import { RootAction } from '../util';
import { RouteParts } from '../reducers';

type HrefTarget = {
  search:
    | {
        [key: string]: string;
      }
    | string;
  href: string;
};

type SheetRouter = (
  route: string,
  parts?: RouteParts,
) => ReturnType<typeof routeChange>;

type RouterDeltaServices = {
  router: SheetRouter;
  param: string;
  location: Location;
  history: History;
};

export const routerDelta = ({
  router,
  param,
  location,
  history,
}: RouterDeltaServices) => (
  actions$: Observable<RootAction, never>,
): Observable<RootAction, never> => {
  const initial$ = Kefir.later(0, router(getRoute(location.search, param)));

  const pushState$ = actions$.thru(ofType(routeChange)).flatMap(({ payload }) =>
    Kefir.stream<never, never>(emitter => {
      const dest = getUrl(param, payload);

      if (dest !== location.pathname + location.search) {
        history.pushState({}, '', dest);
      }

      emitter.end();
    }),
  );

  const href$ = Kefir.stream<RootAction, never>(emitter => {
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
    hist(emit);
  });

  return Kefir.merge([initial$, pushState$, href$]);
};
