// @flow
import type { MessagesState, MessagesFetchSucceededAction } from '../types';
import R from 'ramda';
import combineActionReducers from './combineActionReducers';
import { MESSAGES_FETCH_SUCCEEDED } from '../actions';

const defaults : MessagesState = [];

const cond = [
    [MESSAGES_FETCH_SUCCEEDED, (state: MessagesState, action: MessagesFetchSucceededAction): MessagesState => {
        const newState = state.concat(action.payload.response.messages);

        return R.uniqBy(R.prop('ID'), newState);
    }]
];

export default combineActionReducers(cond, defaults);
