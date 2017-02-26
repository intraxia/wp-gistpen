// @flow
import '../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals } from '../reducer';
import { applyDelta, tinymcePluginDelta, webpackDelta } from '../delta';

createStore(
    combineReducers({ globals }),
    applyDelta(
        tinymcePluginDelta,
        webpackDelta
    )
);
