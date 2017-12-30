// @flow
import type { HasPrismState } from '../../types';
import type { Emitter, Observable } from 'kefir';
import { fromPromise, stream } from 'kefir';
import { component, raf$ } from 'brookjs';
import Prism from '../../prism';

export default component({
    onMount: function onMount(el : Element, props$ : Observable<HasPrismState>) : Observable<void> {
        const original : Element = el.cloneNode(true);
        Prism.setAutoloaderPath(__webpack_public_path__);

        return props$.flatMapLatest((props : HasPrismState) : Observable<void> => {
            const promise = Prism.setTheme(props.prism.theme).then(() => Promise.all([
                Prism.togglePlugin('line-numbers', props.prism['line-numbers']),
                Prism.togglePlugin('show-invisibles', props.prism['show-invisibles'])
            ]));

            return fromPromise(promise).flatMap(() => raf$.take(1).flatMap(() : Observable<void> => stream((emitter : Emitter<void, void>) => {
                const updated = original.cloneNode(true);

                Array.from(updated.querySelectorAll(
                    'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'
                ))
                    .forEach((element : Element) : void => Prism.highlightElement(element, false));

                Array.from(el.children)
                    .forEach((child : Element, idx : number) => {
                        if (!updated.children[idx].isEqualNode(child)) {
                            el.replaceChild(updated.children[idx], child);
                        }
                    });

                emitter.end();
            })));
        });
    }
});
