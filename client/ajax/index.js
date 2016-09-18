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
export default function ajax$(url, opts = {}) {
    return stream(emitter => {
        const options = makeOptions(opts);
        let xhr = new XMLHttpRequest();

        xhr.onload = () =>
            emitter.value('response' in xhr ? xhr.response : xhr.responseText);

        xhr.onerror = () =>
            emitter.error(new TypeError('Network request failed'));

        xhr.ontimeout = () =>
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

        return () => xhr.abort();
    }).take(1).takeErrors(1).setName('ajax$');
}
