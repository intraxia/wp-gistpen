import React from 'react';
import { ofType } from 'brookjs';
import {
  TextControl,
  Button,
  ErrorNotice,
  actions as wpActions,
} from '../../../wp';
import { Choosing } from '../../../search';
import CreateOrChoose from '../CreateOrChoose';
import {
  createRepoClick,
  createDescriptionChange,
  createFilenameChange,
} from '../../actions';
import {
  State,
  ChooseOrNewRepoState,
  ChooseRepoState,
  NewRepoState,
} from './state';

const FilenameInput: React.FC<{ disabled: boolean; value: string }> = ({
  disabled,
  value,
}) => {
  return (
    <TextControl
      label="Snippet filename"
      disabled={disabled}
      value={value}
      preplug={a$ =>
        a$
          .thru(ofType(wpActions.change))
          .map(action => createFilenameChange(action.payload.value))
      }
    />
  );
};

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

const ChooseRepo: React.FC<ChooseRepoState> = ({ filename, saving, error }) => {
  return (
    <div data-testid="choose-existing">
      {error != null && <ErrorNotice>{error.message}</ErrorNotice>}
      <FilenameInput disabled={saving} value={filename} />
      <Choosing collection="repos" disabled={saving || filename === ''} />
    </div>
  );
};

const CreateNew: React.FC<NewRepoState> = ({
  description,
  filename,
  saving,
  error,
}) => {
  return (
    <div data-testid="create-new">
      <TextControl
        label="Gistpen description"
        disabled={saving}
        value={description}
        preplug={a$ =>
          a$
            .thru(ofType(wpActions.change))
            .map(action => createDescriptionChange(action.payload.value))
        }
      />
      <FilenameInput disabled={saving} value={filename} />
      <Button
        isPrimary
        disabled={!description || saving}
        preplug={a$ => a$.thru(ofType(wpActions.click)).map(createRepoClick)}
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
