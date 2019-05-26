import { combineReducers, createStore } from 'redux';
import { StateType } from 'typesafe-actions';
import { ajax$ } from '../../ajax';
import {
  applyDelta,
  searchDelta,
  tinymcePluginDelta,
  webpackDelta
} from '../../deltas';
import { ajaxReducer, globalsReducer, searchReducer } from '../../reducers';
import { RootAction } from '../../util';

const reducer = combineReducers({
  ajax: ajaxReducer,
  globals: globalsReducer,
  search: searchReducer
});

export type State = StateType<typeof reducer>;

export default createStore(
  reducer,
  applyDelta<RootAction, State>(
    searchDelta({ ajax$ }),
    tinymcePluginDelta,
    webpackDelta
  )
);
