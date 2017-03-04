// @flow
import type { Action, Delta, TinyMCEState } from '../type';
import './index.scss';
import '../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals, search } from '../reducer';
import { applyDelta, createViewDelta, tinymcePluginDelta, webpackDelta } from '../delta';
import root from './root';
import getElement from './getElement';

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
    },
    search: { term: '' }
} } = global;

createStore(
    combineReducers({ globals, search }),
    __GISTPEN_TINYMCE__,
    applyDelta(
        tinymcePluginDelta,
        (createViewDelta({ root, getElement }) : Delta<TinyMCEState, Action>),
        webpackDelta
    )
);
