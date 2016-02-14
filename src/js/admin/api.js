import { polyfill } from 'es6-promise';
import fetchival from 'fetchival';
import assignDeep from 'object-assign-deep';
import { Observable, Observer, Subject } from 'rx';
import { resetSiteState, displayApiError, displayApiSuccess } from './actions';

/**
 * Only overwrite the built-in Promise if it's not already there.
 */
if (!window.Promise) {
    polyfill();
}

/**
 * Api fetch object, with settings.
 *
 * @todo remove dependency on globals
 */
const client = fetchival(`${window.Gistpen_Settings.root}site`, {
    credentials: 'include',
    headers: {
        'X-WP-Nonce': window.Gistpen_Settings.nonce
    }
});

/**
 * Creates an api stream from the stream of patches
 * from the application.
 *
 * @param {Observable} patchStream
 * @returns {Subject}
 */
export function create(patchStream) {
    const apiStream = new Subject();
    const generator = createSinglePatchGenerator(patchStream);

    generator().subscribe(createSingleUserObserver());

    return apiStream;

    /**
     * Create an Observer to listen to the single patch stream.
     *
     * @returns {Observer}
     */
    function createSingleUserObserver() {
        return Observer.create(
            ({ site }) => {
                const next = generator();

                patch(
                    site,
                    apiStream
                ).subscribe(() => next
                    .subscribe(createSingleUserObserver())
                )
            }
        );
    }
}

/**
 * Creates a function which returns a once-time debounced stream
 * of patch changes from application.
 *
 * @param {Observable} stream
 * @returns {Function}
 */
function createSinglePatchGenerator(stream) {
    return function () {
        return stream
            .scan((next, incoming) => assignDeep({}, next, incoming), {})
            .debounce(1500)
            .take(1);
    }
}

/**
 * Sends the patch to the server and returns an observable
 * stream of the results.
 *
 * @param {Object} site - Site patch data to send.
 * @param {Observer} observer
 * @returns {Observable}
 */
function patch(site, observer) {
    const observable = Observable.startAsync(
        () => client.patch(site)
    );

    observable.subscribe(
        createApiObserver(observer)
    );

    return observable;
}

/**
 * Creates an Observer wrapper to ensure the subject
 * isn't killed at the end of the patch stream.
 *
 * @param {Observer} observer
 * @returns {Observer}
 */
function createApiObserver(observer) {
    return Observer.create(
        (site) => observer.onNext(resetSiteState(site)),
        (error) => observer.onNext(displayApiError(error.message)),
        () => observer.onNext(displayApiSuccess())
    );
}
