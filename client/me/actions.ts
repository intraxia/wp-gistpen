import { DeepPartial } from 'redux';
import { createAsyncAction } from 'typesafe-actions';
import { AjaxError } from '../api';
import { ApiMe } from './types';

export const fetchMe = createAsyncAction(
  'FETCH_ME_REQUESTED',
  'FETCH_ME_SUCCEEDED',
  'FETCH_ME_FAILED',
)<void, ApiMe, AjaxError>();

export const saveMe = createAsyncAction(
  'SAVE_ME_REQUESTED',
  'SAVE_ME_SUCCEEDED',
  'SAVE_ME_FAILED',
)<DeepPartial<ApiMe>, ApiMe, AjaxError>();
