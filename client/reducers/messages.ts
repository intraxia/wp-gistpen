import { getType } from 'typesafe-actions';
import { EddyReducer } from 'brookjs';
import { messagesFetchSucceeded } from '../actions';
import { RootAction } from '../util';
import { Message } from './jobs';

export type MessagesState = {
  items: {
    [key: string]: Message;
  };
};

const defaultState: MessagesState = {
  items: {}
};

export const messagesReducer: EddyReducer<MessagesState, RootAction> = (
  state = defaultState,
  action
) => {
  switch (action.type) {
    case getType(messagesFetchSucceeded):
      return {
        ...state,
        items: action.payload.response.messages.reduce(
          (items, msg) => ({
            ...items,
            [msg.ID]: msg
          }),
          state.items
        )
      };
    default:
      return state;
  }
};
