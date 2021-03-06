import { combineReducers } from 'brookjs';
import { StateType } from 'typesafe-actions';
import {
  ajaxReducer,
  routeReducer,
  prismReducer,
  gistReducer,
  jobsReducer,
  runsReducer,
  messagesReducer,
} from '../../reducers';
import { globalsReducer } from '../../globals';

export const reducer = combineReducers({
  ajax: ajaxReducer,
  globals: globalsReducer,
  route: routeReducer,
  prism: prismReducer,
  gist: gistReducer,
  jobs: jobsReducer,
  runs: runsReducer,
  messages: messagesReducer,
});

export type State = StateType<typeof reducer>[0];
