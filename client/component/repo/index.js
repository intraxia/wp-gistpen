import { fromPromise, stream } from 'kefir';
import Prism from '../../prism';
import component from 'brookjs/component';

export default component({
    onMount: function onMount(el, props$) {
        const original = el.cloneNode(true);
        Prism.setAutoloaderPath(__webpack_public_path__);

        return props$.flatMapLatest(props => {
            const promise = Prism.setTheme(props.prism.theme).then(() => Promise.all([
                Prism.togglePlugin('line-numbers', props.prism['line-numbers']),
                Prism.togglePlugin('show-invisibles', props.prism['show-invisibles'])
            ]));

            return fromPromise(promise)
                .flatMap(() => stream(emitter => {
                    let loop = requestAnimationFrame(() => {
                        const updated = original.cloneNode(true);

                        Array.from(updated.querySelectorAll(
                            'code[class*="language-"], [class*="language-"] code, code[class*="lang-"], [class*="lang-"] code'
                        ))
                            .forEach(element => Prism.highlightElement(element, false));

                        Array.from(el.children)
                            .forEach((child, idx) => {
                                if (!updated.children[idx].isEqualNode(child)) {
                                    el.replaceChild(updated.children[idx], child);
                                }
                            });

                        emitter.end();
                    });

                    return () => cancelAnimationFrame(loop);
                }));
        });
    }
});
