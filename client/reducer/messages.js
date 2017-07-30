// @flow
import type { MessagesState } from '../type';
import { combineActionReducers } from 'brookjs';

const defaults : MessagesState = [];

const cond = [];

export default combineActionReducers(cond, defaults);
