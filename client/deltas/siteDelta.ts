import Kefir, { Observable } from 'kefir';
import { ajax$ } from 'kefir-ajax';
import { ajaxStarted, ajaxFailed, ajaxFinished } from '../actions';
import { RootAction } from '../RootAction';
import { GistState, PrismState } from '../reducers';
import { GlobalsState } from '../globals';

type SiteDeltaState = {
  globals: GlobalsState;
  gist: GistState;
  prism: PrismState;
};

type SiteDeltaServices = {
  ajax$: typeof ajax$;
};

export const siteDelta = ({ ajax$ }: SiteDeltaServices) => (
  action$: Observable<RootAction, never>,
  state$: Observable<SiteDeltaState, never>,
): Observable<RootAction, never> =>
  state$
    .skip(1)
    .skipDuplicates(
      (prev, next) => prev.gist === next.gist && prev.prism === next.prism,
    )
    .debounce(1000)
    .flatMapLatest(state =>
      Kefir.concat<RootAction, never>([
        Kefir.constant(ajaxStarted()),
        ajax$(state.globals.root + 'site', {
          method: 'PATCH',
          body: JSON.stringify({
            gist: state.gist,
            prism: state.prism,
          }),
          credentials: 'include',
          headers: {
            'X-WP-Nonce': state.globals.nonce,
            'Content-Type': 'application/json',
          },
        })
          .flatMap(() => Kefir.constant(ajaxFinished()))
          .flatMapErrors(err => Kefir.constant(ajaxFailed(err))),
      ]),
    );
