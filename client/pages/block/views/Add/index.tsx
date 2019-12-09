import React, { useEffect } from 'react';
import { ButtonGroup, Button, TextControl } from '@wordpress/components';
import { useDeltas } from 'brookjs-silt';
import Back from '../Back';
import {
  addToNewBtnClick,
  addToExistingBtnClick,
  descriptionChange,
  saveNewBtnClick,
  searchChange,
  backClick
} from './actions';
import delta from './delta';
import reducer from './reducer';
import { initialState } from './state';

const Add: React.FC<{
  onBackClick: () => void;
  setIds: (repoId: number, blobId: number) => void;
}> = ({ onBackClick, setIds }) => {
  const { state, dispatch } = useDeltas(reducer, initialState, [delta]);

  useEffect(() => {
    if (state.repoId != null && state.blobId != null) {
      setIds(state.repoId, state.blobId);
    }
  }, [state.repoId, state.blobId]);

  switch (state.view) {
    case 'choose':
      return (
        <div>
          <h2>Add to what?</h2>
          <ButtonGroup>
            <Button
              data-testid="block-add-to-new-btn"
              onClick={() => dispatch(addToNewBtnClick())}
            >
              New
            </Button>
            <Button
              data-testid="block-add-to-existing-btn"
              onClick={() => dispatch(addToExistingBtnClick())}
            >
              Existing
            </Button>
          </ButtonGroup>
          <Back onClick={onBackClick} />
        </div>
      );
    case 'new':
      return (
        <div>
          <h2>Add to new</h2>
          <TextControl
            label="Description"
            value={state.description}
            onChange={value => dispatch(descriptionChange(value))}
          />
          <ButtonGroup>
            <Button
              onClick={() => dispatch(saveNewBtnClick())}
              data-testid="block-save-new-btn"
            >
              Save
            </Button>
            <Back onClick={() => dispatch(backClick())} />
          </ButtonGroup>
          {state.error != null && <pre>{state.error.message}</pre>}
        </div>
      );
    case 'existing':
      return (
        <div>
          <h2>Add to existing</h2>
          <TextControl
            label="Search"
            value={state.search}
            onChange={value => dispatch(searchChange(value))}
          />
          <ButtonGroup>
            <Back onClick={() => dispatch(backClick())} />
          </ButtonGroup>
        </div>
      );
  }
};

export default Add;
