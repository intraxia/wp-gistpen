import React from 'react';
import { render } from 'react-dom';
import { eventStream } from './streams';
import patcher from './patcher';
import { create as createStore } from './store';
import App from './app';

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

    /**
     * Keep the store updated with the stream of patches.
     */
    patchStream.subscribe(storeStream.patch);

    /**
     * Keep the UI in sync with the latest store data.
     */
    storeStream.subscribe((props) => render(
        <App {...props} />,
        mountpoint
    ));
}
