// @flow
import type { Action, Delta, TinyMCEState } from '../type';
import './index.scss';
import '../polyfills';
import { createStore, combineReducers } from 'redux';
import { globals, search } from '../reducer';
import { applyDelta, searchDelta, createViewDelta, tinymcePluginDelta, webpackDelta } from '../delta';
import root from './root';
import getElement from './getElement';

const { __GISTPEN_TINYMCE__ } = global;

createStore(
    combineReducers({ globals, search }),
    __GISTPEN_TINYMCE__,
    applyDelta(
        searchDelta,
        tinymcePluginDelta,
        (createViewDelta({ root, getElement }) : Delta<TinyMCEState, Action>),
        webpackDelta
    )
);
