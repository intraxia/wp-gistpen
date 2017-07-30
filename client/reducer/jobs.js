// @flow
import type { JobsState } from '../type';
import { combineActionReducers } from 'brookjs';

const defaults : JobsState = {};

const cond = [];

export default combineActionReducers(cond, defaults);
