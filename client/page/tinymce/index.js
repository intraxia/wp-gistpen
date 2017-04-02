// @flow
import './index.scss';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals, search } from '../../reducer';
import { applyDelta, searchDelta, tinymcePluginDelta, webpackDelta } from '../../delta';

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
