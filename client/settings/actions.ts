import { createAsyncAction } from 'typesafe-actions';
import { AjaxError, PatchBody } from '../api';
import { ApiSettings } from './types';

export const fetchSettings = createAsyncAction(
  'FETCH_SETTINGS_REQUESTED',
  'FETCH_SETTINGS_SUCCEEDED',
  'FETCH_SETTINGS_FAILED',
)<void, ApiSettings, AjaxError>();

export const saveSettings = createAsyncAction(
  'SAVE_SETTINGS_REQUESTED',
  'SAVE_SETTINGS_SUCCEEDED',
  'SAVE_SETTINGS_FAILED',
)<PatchBody<ApiSettings>, ApiSettings, AjaxError>();
