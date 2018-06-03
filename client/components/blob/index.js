// @flow
import type { Observable, Emitter } from 'kefir';
import type { HasPrismState } from '../../types';
import { component, Kefir, raf$ } from 'brookjs';
import Prism from '../../prism';

export default component({
    onMount: (el, props$): Observable<void> => {
        Prism.setAutoloaderPath(__webpack_public_path__);

        return props$.flatMapLatest((props: HasPrismState): Observable<void> => {
            const promise = Promise.all([
                Prism.setTheme(props.prism.theme),
                Prism.togglePlugin('line-numbers', props.prism['line-numbers']),
                Prism.togglePlugin('show-invisibles', props.prism['show-invisibles'])
            ]);

            return Kefir.fromPromise(promise).flatMap(() => raf$.take(1).flatMap((): Observable<void> => Kefir.stream((emitter: Emitter<void, void>) => {
                Prism.highlightElement(el.querySelector('code'), false);

                emitter.end();
            })));
        });
    }
});
