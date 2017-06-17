// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { domDelta } from 'brookjs';
import { applyDelta, repoDelta, revisionsDelta, userDelta } from '../../delta';
import { el, view } from './dom';
import { api, editor, repo, route } from '../../reducer';
import { selectEditorProps as selectProps } from '../../selector';
import { ajax$ } from '../../service';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.api.url + 'assets/js/';

createStore(
    combineReducers({ api, editor, repo, route }),
    __GISTPEN_EDITOR__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        repoDelta,
        revisionsDelta({ ajax$ }),
        userDelta
    )
);
