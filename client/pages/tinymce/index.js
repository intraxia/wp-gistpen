// @flow
import './index.scss';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals, search } from '../../reducers';
import { applyDelta, searchDelta, tinymcePluginDelta, webpackDelta } from '../../deltas';

const { __GISTPEN_TINYMCE__ } = global;

createStore(
    combineReducers({ globals, search }),
    __GISTPEN_TINYMCE__,
    applyDelta(
        searchDelta,
        tinymcePluginDelta,
        webpackDelta
    )
);
