// @flow
import '../../polyfills';
import { createStore, combineReducers } from 'redux';
import { domDelta } from 'brookjs';
import { applyDelta, repoDelta, revisionsDelta, routerDelta, userDelta } from '../../delta';
import { api, editor, repo, revisions, route } from '../../reducer';
import { selectEditorProps as selectProps } from '../../selector';
import { ajax$ } from '../../service';
import { el, view } from './dom';
import router from './router';

const { __GISTPEN_EDITOR__ } = global;

// eslint-disable-next-line camelcase
__webpack_public_path__ = __GISTPEN_EDITOR__.api.url + 'assets/js/';

createStore(
    combineReducers({ api, editor, revisions, repo, route }),
    __GISTPEN_EDITOR__,
    applyDelta(
        domDelta({ el, selectProps, view }),
        repoDelta,
        routerDelta({ router, param: 'wpgp_route' }),
        revisionsDelta({ ajax$ }),
        userDelta
    )
);
