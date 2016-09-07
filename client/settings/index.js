import R from 'ramda';
import { createStore } from 'redux';
import { fromESObservable } from 'kefir';
import root from './root';
import delta from '../delta';
import middleware from '../middleware';
import reducer from '../reducer';

const { __GISTPEN_STATE__ } = global;

let enhancer = R.pipe(delta, middleware);

if (process.env.NODE_ENV !== 'production') {
    // To use devtools, install Chrome extension:
    // https://chrome.google.com/webstore/detail/redux-devtools/lmhkpmbekcpmknklioeibfkpmmfibljd
    enhancer = R.pipe(global.devToolsExtension ? global.devToolsExtension() : R.identity, enhancer);
}

document.addEventListener('DOMContentLoaded', () => {
    const element = document.querySelector('[data-brk-container="settings"]');
    const store = createStore(reducer, __GISTPEN_STATE__, enhancer);
    const state$ = fromESObservable(store).toProperty(store.getState);

    const app$ = root(element, state$);

    if (process.env.NODE_ENV !== 'production') {
        app$.log('app$');
    }

    app$.observe({ value: store.dispatch });
});
