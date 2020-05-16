import React from 'react';
import { ofType } from 'brookjs';
import { TextControl, Button, ErrorNotice } from '../../../wp';
import { Choosing } from '../../../search';
import CreateOrChoose from '../CreateOrChoose';
import { click, change } from '../../../actions';
import { createRepoClick, createDescriptionChange } from '../../actions';
import {
  State,
  ChooseOrNewRepoState,
  ChooseRepoState,
  NewRepoState,
} from './state';

const ChooseOrNewRepo: React.FC<ChooseOrNewRepoState> = () => {
  return (
    <div data-testid="choose-or-new-repo">
      <CreateOrChoose
        header="Where do you want to add the new snippet?"
        createLabel="Create new repo"
        chooseLabel="Add to existing repo"
      />
    </div>
  );
};

const ChooseRepo: React.FC<ChooseRepoState> = ({ saving, error }) => {
  return (
    <div data-testid="choose-existing">
      {error != null && <ErrorNotice>{error.message}</ErrorNotice>}
      <Choosing collection="repos" disabled={saving} />
    </div>
  );
};

const CreateNew: React.FC<NewRepoState> = ({ description, saving, error }) => {
  return (
    <div data-testid="create-new">
      <TextControl
        label="Gistpen description"
        disabled={saving}
        value={description}
        preplug={a$ =>
          a$
            .thru(ofType(change))
            .map(action => createDescriptionChange(action.payload.value))
        }
      />
      <Button
        isPrimary
        disabled={!description || saving}
        preplug={a$ => a$.thru(ofType(click)).map(createRepoClick)}
      >
        Create repo
      </Button>
      {error != null && <ErrorNotice>{error.message}</ErrorNotice>}
    </div>
  );
};

export const View: React.FC<State> = state => {
  return (
    <div data-testid="creating">
      {state.status === 'choose-or-new-repo' && <ChooseOrNewRepo {...state} />}
      {state.status === 'choose-existing' && <ChooseRepo {...state} />}
      {state.status === 'create-new' && <CreateNew {...state} />}
    </div>
  );
};
