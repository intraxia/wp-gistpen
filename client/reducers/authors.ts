import { getType } from 'typesafe-actions';
import { fetchAuthorSucceeded } from '../actions';
import { RootAction } from '../util';

export type Author = {
  id: number;
  name: string;
  url: string;
  description: string;
  link: string;
  slug: string;
  avatar_urls: {
    [key: string]: string;
  };
};

export type AuthorsState = {
  items: {
    [key: string]: Author;
  };
};

const defaultState: AuthorsState = {
  items: {}
};

export const authorsReducer = (
  state: AuthorsState = defaultState,
  action: RootAction
) => {
  switch (action.type) {
    case getType(fetchAuthorSucceeded):
      return {
        ...state,
        items: {
          ...state.items,
          [action.payload.author.id]: action.payload.author
        }
      };
    default:
      return state;
  }
};
