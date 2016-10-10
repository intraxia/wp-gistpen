import R from 'ramda';
import { applyMiddleware } from 'redux';
import { observeDelta } from 'brookjs';

export { default as createRouterDelta } from './router';
export { default as siteDelta } from './site';

export const applyDelta = R.pipe(observeDelta, applyMiddleware);
