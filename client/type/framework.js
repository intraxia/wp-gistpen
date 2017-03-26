// @flow
import { Observable } from 'kefir';

export class ActionObservable<V, E=*> extends Observable<V, E> {
    ofType : (...types : Array<string>) => Observable<V, E>
}

export type Delta<A, S> = (actions$ : ActionObservable<A>, state$ : Observable<S>) => Observable<A>;

export type ComponentFactory<P, A> = (el : Element, props$ : Observable<P>) => Observable<A>;

export type ViewDeltaConfig<P, A> = {
    root : ComponentFactory<P, A>;
    getElement : () => Observable<Element, Error>;
};
