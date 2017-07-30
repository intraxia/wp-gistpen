// @flow
import type { MessagesState, MessagesFetchSucceededAction } from '../type';
import R from 'ramda';
import { combineActionReducers } from 'brookjs';
import { MESSAGES_FETCH_SUCCEEDED } from '../action';

const defaults : MessagesState = [];

const cond = [
    [MESSAGES_FETCH_SUCCEEDED, (state : MessagesState, action : MessagesFetchSucceededAction) : MessagesState => {
        const newState = state.concat(action.payload.response.messages);

        return R.uniqBy(R.prop('ID'), newState);
    }]
];

export default combineActionReducers(cond, defaults);
