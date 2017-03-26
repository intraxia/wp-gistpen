// @flow
import './index.scss';
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { domDelta } from 'brookjs';
import { globals, search } from '../../reducer';
import { applyDelta, searchDelta, tinymcePluginDelta, webpackDelta } from '../../delta';
import component from './component';
import el from './el';
import { selectTinyMCEProps as selectProps } from '../../selector';

const { __GISTPEN_TINYMCE__ } = global;

createStore(
    combineReducers({ globals, search }),
    __GISTPEN_TINYMCE__,
    applyDelta(
        domDelta({ component, el, selectProps }),
        searchDelta,
        tinymcePluginDelta,
        webpackDelta
    )
);
