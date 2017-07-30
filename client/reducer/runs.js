// @flow
import type { RunsState } from '../type';
import { combineActionReducers } from 'brookjs';

const defaults : RunsState = [];

const cond = [];

export default combineActionReducers(cond, defaults);
