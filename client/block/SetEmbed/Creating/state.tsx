import { EddyReducer, unreachable, loop, Maybe, Result } from 'brookjs';
import { getType } from 'typesafe-actions';
import { RootAction } from '../../../util';
import { actions as searchActions } from '../../../search';
import {
  createNewClick,
  chooseExistingClick,
  appendBlob,
  newBlobAttached,
  createDescriptionChange,
  createRepoClick,
  createRepo,
  newRepoCreated,
} from '../../actions';
import { GlobalsState, defaultGlobals } from '../../../globals';
import { AjaxError } from '../../../api';

export type ChooseOrNewRepoState = {
  globals: GlobalsState;
  status: 'choose-or-new-repo';
};

export type ChooseRepoState = {
  globals: GlobalsState;
  status: 'choose-existing';
  saving: boolean;
  error: Maybe<AjaxError>;
};

export type NewRepoState = {
  globals: GlobalsState;
  status: 'create-new';
  description: string;
  saving: boolean;
  error: Maybe<AjaxError>;
};

export type State = ChooseOrNewRepoState | ChooseRepoState | NewRepoState;

export const initialState: State = {
  globals: defaultGlobals,
  status: 'choose-or-new-repo',
};

export const reducer: EddyReducer<State, RootAction> = (
  state: State,
  action: RootAction,
): State | Result<State, RootAction> => {
  switch (state.status) {
    case 'choose-or-new-repo':
      switch (action.type) {
        case getType(createNewClick):
          return {
            globals: state.globals,
            status: 'create-new',
            description: '',
            saving: false,
            error: null,
          } as const;
        case getType(chooseExistingClick):
          return {
            globals: state.globals,
            status: 'choose-existing',
            saving: false,
            error: null,
          } as const;
        default:
          return state;
      }
    case 'choose-existing':
      switch (action.type) {
        case getType(searchActions.searchRepoSelected):
          return loop(state, appendBlob.request(action.payload.repo.ID));
        case getType(appendBlob.request):
          return {
            ...state,
            saving: true,
          };
        case getType(appendBlob.failure):
          return {
            ...state,
            saving: false,
            error: action.payload,
          };
        case getType(appendBlob.success):
          return loop(
            {
              ...state,
              saving: false,
              error: null,
            },
            newBlobAttached(action.payload.blob.ID, action.payload.repoId),
          );
        default:
          return state;
      }
    case 'create-new':
      switch (action.type) {
        case getType(createDescriptionChange):
          return {
            ...state,
            description: action.payload.value,
          } as const;
        case getType(createRepoClick):
          return loop(state, createRepo.request());
        case getType(createRepo.request):
          return {
            ...state,
            saving: true,
          } as const;
        case getType(createRepo.success):
          return loop(
            {
              ...state,
              saving: false,
              error: null,
            } as const,
            newRepoCreated(action.payload),
          );
        case getType(createRepo.failure):
          return {
            ...state,
            saving: false,
            error: action.payload,
          } as const;
        default:
          return state;
      }
    default:
      return unreachable(state);
  }
};
