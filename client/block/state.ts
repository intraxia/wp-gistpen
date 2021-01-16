import { EddyReducer, Maybe, unreachable } from 'brookjs';
import { getType } from 'typesafe-actions';
import { RootAction } from '../RootAction';
import { embedSet, highlightChange, offsetChange } from './actions';

export type SetEmbed = {
  status: 'set-embed';
  repoId: Maybe<number>;
  blobId: Maybe<number>;
  highlight: Maybe<string>;
  offset: Maybe<number>;
};

export type EditBlob = {
  status: 'edit-embed';
  repoId: number;
  blobId: number;
  highlight: string;
  offset: number;
};

export type State = SetEmbed | EditBlob;

export type Attributes = {
  blobId: Maybe<number>;
  repoId: Maybe<number>;
  highlight?: Maybe<string>;
  offset?: Maybe<number>;
};

// @TODO(mAAdhaTTah) remove duplicate types on params (implicit any?!)
export const reducer: EddyReducer<State, RootAction> = (
  state: State,
  action: RootAction,
) => {
  switch (state.status) {
    case 'set-embed':
      switch (action.type) {
        case getType(embedSet):
          return {
            status: 'edit-embed',
            repoId: action.payload.repoId,
            blobId: action.payload.blobId,
            highlight: state.highlight ?? '',
            offset: state.offset ?? 0,
          } as const;
        default:
          return state;
      }
    case 'edit-embed':
      switch (action.type) {
        case getType(highlightChange):
          return {
            ...state,
            highlight: action.payload.highlight,
          };
        case getType(offsetChange):
          return {
            ...state,
            offset: action.payload.offset,
          };
        default:
          return state;
      }
    default:
      return unreachable(state);
  }
};

export const initialState = ({
  blobId,
  repoId,
  highlight,
  offset,
}: Attributes): State => {
  if (blobId == null || repoId == null) {
    return {
      status: 'set-embed',
      repoId,
      blobId,
      highlight,
      offset,
    } as const;
  } else {
    return {
      status: 'edit-embed',
      repoId,
      blobId,
      highlight: highlight ?? '',
      offset: offset ?? 0,
    } as const;
  }
};
