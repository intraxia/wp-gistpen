import { getType } from 'typesafe-actions';
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

export const messagesReducer = (
  state: MessagesState = defaultState,
  action: RootAction
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
