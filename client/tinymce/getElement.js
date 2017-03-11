// @flow
import type { Emitter, Observable } from 'kefir';
import { stream } from 'kefir';

export default () : Observable<Element, Error> => stream((emitter : Emitter<Element, Error>) => {
    const element = document.body;

    if (!element) {
        emitter.error(new Error('body not found on document'));
    } else {
        element.setAttribute('data-brk-container', 'body');
        emitter.value(element);
    }

    emitter.end();
});
