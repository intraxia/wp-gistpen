// @flow
import type { AuthorsState } from '../types';
import type { FetchAuthorSucceeded } from '../actions';
import { combineActionReducers } from 'brookjs';
import { FETCH_AUTHOR_SUCCEEDED } from '../actions';

const defaults : AuthorsState = {
    items: {}
};

const cond = [
    [FETCH_AUTHOR_SUCCEEDED, (state : AuthorsState, { payload } : FetchAuthorSucceeded) => ({
        ...state,
        items: {
            ...state.items,
            [payload.author.id]: payload.author
        }
    })]
];

export default combineActionReducers(cond, defaults);
