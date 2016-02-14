import React from 'react';
import { render } from 'react-dom';
import { Observable } from 'rx';
import { eventStream } from './streams';
import patcher from './patcher';
import { create as createStore } from './store';
import { create as createApi } from './api';
import App from './app';

const clearMessage = {message: {status: false}};

/**
 * Bootstrap the application with its given state and root node.
 *
 * @param {Object} initial - Initial application data state
 * @param {Element} mountpoint - DOM node to mount application
 */
export function bootstrap(initial, mountpoint) {
    /**
     * Create our data flow streams.
     */
    const patchStream = eventStream.map(patcher);
    const storeStream = createStore(initial);
    const apiStream = createApi(patchStream).map(patcher);
    const statusResetStream = apiStream
        .filter((patch) => !!patch.message)
        .debounce(3500)
        .map(() => clearMessage);

    /**
     * Keep the store updated with the combined stream of patches.
     */
    Observable.merge(
        patchStream,
        apiStream,
        statusResetStream
    ).subscribe(storeStream.patch);

    /**
     * Keep the UI in sync with the latest store data.
     */
    storeStream.subscribe(
        (props) => render(
            <App {...props} />,
            mountpoint
        )
    );
}
