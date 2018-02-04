// @flow
import type { Observable } from 'kefir';

export type Delta<A, S> = (actions$: Observable<A>, state$: Observable<S>) => Observable<A>;

export type ComponentFactory<P, A> = (el: Element, props$: Observable<P>) => Observable<A>;

export type ViewDeltaConfig<P, A> = {
    root: ComponentFactory<P, A>;
    getElement: () => Observable<Element, Error>
};
