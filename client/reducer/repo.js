// @flow
import type { RepoSaveSucceededAction, Repo } from '../type';
import { combineActionReducers } from 'brookjs';
import { REPO_SAVE_SUCCEEDED } from '../action';

export default combineActionReducers([
    [REPO_SAVE_SUCCEEDED, (state : Repo, { payload } : RepoSaveSucceededAction) => payload.response]
], {});
