// @flow
import type { RepoSaveSucceededAction, Repo } from '../types';
import { combineActionReducers } from 'brookjs';
import { REPO_SAVE_SUCCEEDED } from '../actions';

export default combineActionReducers([
    [REPO_SAVE_SUCCEEDED, (state : Repo, { payload } : RepoSaveSucceededAction) => payload.response]
], {});
