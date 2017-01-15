// @flow
import type { AjaxOptions } from '../type';
import type { Emitter, Observable } from 'kefir';
import R from 'ramda';
import { stream } from 'kefir';

const makeOptions = R.merge({
    method: 'GET',
    headers: {}
});

/**
 * Create a new ajax request stream.
 *
 * @param {string} url - Url to request.
 * @param {Object} opts - Request options.
 * @returns {Stream<T, S>} Ajax stream.
 */
export default function ajax$(url : string , opts : AjaxOptions) : Observable<string, TypeError> {
    return stream((emitter : Emitter<string, TypeError>) : (() => void) => {
        const options = makeOptions(opts);
        let xhr = new XMLHttpRequest();

        xhr.onload = () : boolean =>
            emitter.value('response' in xhr ? xhr.response : xhr.responseText);

        xhr.onerror = () : boolean =>
            emitter.error(new TypeError('Network request failed'));

        xhr.ontimeout = () : boolean =>
            emitter.error(new TypeError('Network request failed'));

        xhr.open(options.method, url, true);

        if (options.credentials === 'include') {
            xhr.withCredentials = true;
        }

        for (let name in options.headers) {
            if (options.headers.hasOwnProperty(name)) {
                xhr.setRequestHeader(name, options.headers[name]);
            }
        }

        xhr.send(typeof options.body !== 'undefined' ? options.body : null);

        return () : void => xhr.abort();
    }).take(1).takeErrors(1);
}
