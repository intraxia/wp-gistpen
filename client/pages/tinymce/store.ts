import { createStore } from 'redux';
import { StateType } from 'typesafe-actions';
import { combineReducers, eddy } from 'brookjs';
import { ajax$ } from '../../ajax';
import {
  applyDelta,
  searchDelta,
  tinymcePluginDelta,
  webpackDelta,
} from '../../deltas';
import { ajaxReducer, searchReducer } from '../../reducers';
import { RootAction } from '../../util';
import { globalsReducer } from '../../globals';

const reducer = combineReducers({
  ajax: ajaxReducer,
  globals: globalsReducer,
  search: searchReducer,
});

export type State = StateType<typeof reducer>[0];

export default eddy()(createStore)(
  reducer,
  applyDelta<RootAction, State>(
    searchDelta({ ajax$ }),
    tinymcePluginDelta,
    webpackDelta,
  ),
);
