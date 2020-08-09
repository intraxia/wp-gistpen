import React from 'react';
import {
  useDelta,
  Result as EddyResult,
  RootJunction,
  unreachable,
  loop,
  toJunction,
  ofType,
} from 'brookjs';
import { getType } from 'typesafe-actions';
import { RootAction } from '../../RootAction';
import { Choosing } from '../../search';
import {
  chooseExistingClick,
  createNewClick,
  embedSet,
  newBlobAttached,
  newRepoCreated,
} from '../actions';
import { searchBlobSelected } from '../../search/actions';
import CreateOrChoose from './CreateOrChoose';
import Creating from './Creating';

type Props = {};

type CreateOrChooseState = {
  status: 'create-or-choose';
};

type ChoosingState = {
  status: 'choosing';
};

type CreatingState = {
  status: 'creating';
};

type State = CreateOrChooseState | ChoosingState | CreatingState;

const createOrChooseState: State = {
  status: 'create-or-choose',
};

const reducer = (
  state: State,
  action: RootAction,
): State | EddyResult<State, RootAction> => {
  switch (state.status) {
    case 'create-or-choose':
      switch (action.type) {
        case getType(chooseExistingClick):
          return {
            status: 'choosing',
          };
        case getType(createNewClick):
          return {
            status: 'creating',
          };
        default:
          return state;
      }
    case 'choosing':
      switch (action.type) {
        case getType(searchBlobSelected):
          return loop(
            state,
            embedSet(action.payload.blob.repo_id, action.payload.blob.ID),
          );
        default:
          return state;
      }
    case 'creating':
      switch (action.type) {
        case getType(newBlobAttached):
          return loop(
            state,
            embedSet(action.payload.repoId, action.payload.blobId),
          );
        case getType(newRepoCreated):
          return loop(
            state,
            embedSet(action.payload.repo.ID, action.payload.repo.blobs[0].ID),
          );
        default:
          return state;
      }
    default:
      return unreachable(state);
  }
};

const SetEmbed: React.FC<Props> = () => {
  const { state, root$ } = useDelta(reducer, createOrChooseState);

  return (
    <RootJunction root$={root$}>
      <div data-testid="set-embed">
        {state.status === 'create-or-choose' && (
          <CreateOrChoose
            header="Do you want to choose an existing snippet or create a new one?"
            createLabel="Create new"
            chooseLabel="Choose from existing"
          />
        )}
        {state.status === 'choosing' && <Choosing collection="blobs" />}
        {state.status === 'creating' && <Creating />}
      </div>
    </RootJunction>
  );
};

export default toJunction(c$ => c$.thru(ofType(embedSet)))(SetEmbed);
