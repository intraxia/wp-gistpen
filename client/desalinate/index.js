// @flow
import type { Emitter, Observable } from 'kefir';
import R from 'ramda';
import { pool, stream } from 'kefir';

type Template = (context: Object) => string;

export const createEl$ = R.curry((template: Template, props: Object = {}, callback: Function = R.identity): Observable<Element, Error> => stream((emitter: Emitter<Element, Error>): Disposer => {
    if (typeof document === 'undefined' || document.body === null) {
        emitter.error(new Error('document or body is not defined'));

        emitter.end();

        return () => {};
    }

    // Flow seems to want the below document.body checks :-/
    // Can't seem to make the errors go away w/o 'em.
    let wrapper = document.createElement('div');
    wrapper.innerHTML = template(props);
    const el = wrapper.firstElementChild || wrapper;
    wrapper = null;
    document.body && document.body.appendChild(el);

    emitter.value(el);

    setTimeout(() => callback(el), 0);

    return () => document.body && document.body.removeChild(el);
}));

export const createProps$ = (callback: Function = R.identity): Observable<Observable<Object>> => stream((emitter: Emitter<Observable<Object>, void>) => {
    const props$ = pool();

    emitter.value(props$);

    setTimeout(() => callback(props$), 0);
});

export const createInstance$ = (
    el$: Observable<Element, Error>,
    props$$: Observable<Observable<Object>>,
    component: (el: Element, props$: Observable<Object>) => Observable<Object>
): Observable<Object> => el$.zip(props$$)
    .flatMap(R.apply(component));
