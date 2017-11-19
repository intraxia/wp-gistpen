// @flow
import type { Emitter } from 'kefir';
import Kefir from 'kefir';

export type AjaxOptions = {
    method : string;
    body? : string;
    credentials? : 'include';
    headers? : {
        [key : string] : string;
    };
};

const defaults : AjaxOptions = {
    method: 'GET',
    headers: {}
};

export class ObsResponse {
    xhr : XMLHttpRequest;

    constructor(xhr : XMLHttpRequest) {
        this.xhr = xhr;
    }

    json<T>() : Kefir.Observable<T, TypeError> {
        const xhr = this.xhr;

        return Kefir.stream(emitter => {
            try {
                emitter.value(JSON.parse('response' in xhr ? xhr.response : xhr.responseText));
            } catch (e) {
                emitter.error(new TypeError(`Error parsing JSON response: ${e.message}`));
            }

            emitter.end();
        });
    }
}

export type AjaxService = (url : string, opts : AjaxOptions) => Kefir.Observable<ObsResponse, TypeError>;

/**
 * Create a new ajax request stream.
 *
 * @param {string} url - Url to request.
 * @param {Object} opts - Request options.
 * @returns {Stream<T, S>} Ajax stream.
 */
export const ajax$ : AjaxService = (
    url : string ,
    opts : AjaxOptions
) : Kefir.Observable<ObsResponse, TypeError> =>
    Kefir.stream((emitter : Emitter<ObsResponse, TypeError>) : (() => void) => {
        const options = { ...defaults, ...opts };
        const xhr = new XMLHttpRequest();

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 400) {
                emitter.value(new ObsResponse(xhr));
            } else {
                emitter.error(new TypeError(`${xhr.status} - ${xhr.statusText}`));
            }
        };

        xhr.onerror = () : boolean =>
            emitter.error(new TypeError('Network request failed'));

        xhr.ontimeout = () : boolean =>
            emitter.error(new TypeError('Network request failed'));

        xhr.open(options.method, url, true);

        if (options.credentials === 'include') {
            xhr.withCredentials = true;
        }

        for (const name in options.headers) {
            if (options.headers.hasOwnProperty(name)) {
                xhr.setRequestHeader(name, options.headers[name]);
            }
        }

        xhr.send(typeof options.body !== 'undefined' ? options.body : null);

        return () : void => xhr.abort();
    }).take(1).takeErrors(1);
