// @flow
import './index.scss';
import '../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals } from '../reducer';
import { applyDelta, tinymcePluginDelta, webpackDelta } from '../delta';

const { __GISTPEN_TINYMCE__ = {
    globals: {
        ace_themes: {},
        ace_widths: [],
        languages: {},
        root: '',
        statuses: {},
        nonce: '',
        url: '',
        themes: {}
    }
} } = global;

createStore(
    combineReducers({ globals }),
    __GISTPEN_TINYMCE__,
    applyDelta(
        tinymcePluginDelta,
        webpackDelta
    )
);
