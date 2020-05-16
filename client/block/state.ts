import { EddyReducer, Maybe } from 'brookjs';
import { getType } from 'typesafe-actions';
import { RootAction } from '../util';
import { embedSet } from './actions';

export type SetEmbed = {
  status: 'set-embed';
  repoId: Maybe<number>;
  blobId: Maybe<number>;
};

export type EditBlob = {
  status: 'edit-embed';
  repoId: number;
  blobId: number;
};

export type State = SetEmbed | EditBlob;

export type Attributes = {
  blobId: Maybe<number>;
  repoId: Maybe<number>;
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
          } as const;
        default:
          return state;
      }
    case 'edit-embed':
      switch (action.type) {
        default:
          return state;
      }
  }
};

export const initialState = ({ blobId, repoId }: Attributes): State => {
  if (blobId == null || repoId == null) {
    return {
      status: 'set-embed',
      repoId,
      blobId,
    } as const;
  } else {
    return {
      status: 'edit-embed',
      repoId,
      blobId,
    } as const;
  }
};
