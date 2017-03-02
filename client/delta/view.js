// @flow
import type { Observable } from 'kefir';
import type { Delta, ViewDeltaConfig } from '../type';
import { constant, fromCallback, fromEvents } from 'kefir';

const documentIsLoaded = () : Observable<boolean> =>
    fromCallback((callback : ((value : boolean) => void)) =>
        callback(
            document.readyState === 'complete' ||
                document.readyState === 'loaded' ||
                document.readyState === 'interactive'
        )
    );

export default function createViewDelta<P,A>({ getElement, root } : ViewDeltaConfig<P, A>) : Delta<A, P> {
    return (actions$ : Observable<A>, state$ : Observable<P>) =>
        documentIsLoaded()
            .flatMap((isLoaded : boolean) =>
                isLoaded ?
                    constant(true) :
                    fromEvents(document, 'DOMContentLoaded')
            )
                .flatMap(getElement)
                .flatMap((el : Element) : Observable<A> => root(el, state$));
}
