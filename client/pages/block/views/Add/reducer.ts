import { getType } from 'typesafe-actions';
import * as actions from './actions';
import { State, Action } from './state';

const reducer = (state: State, action: Action): State => {
  switch (action.type) {
    case getType(actions.addToNewBtnClick):
      return {
        ...state,
        view: 'new'
      };
    case getType(actions.addToExistingBtnClick):
      return {
        ...state,
        view: 'existing'
      };
    case getType(actions.backClick):
      return {
        ...state,
        view: 'choose'
      };
    case getType(actions.descriptionChange):
      return {
        ...state,
        description: action.payload.description
      };
    case getType(actions.saveNewBtnClick):
      return {
        ...state,
        saving: true
      };
    case getType(actions.createRepo.success):
      return {
        ...state,
        saving: false,
        // Existance is validated in delta.
        repoId: action.payload.ID,
        blobId: action.payload.blobs[0].ID
      };
    case getType(actions.createRepo.failure):
      return {
        ...state,
        saving: false,
        error: action.payload
      };
    default:
      return state;
  }
};

export default reducer;
