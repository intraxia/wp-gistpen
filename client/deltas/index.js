// @flow
import R from 'ramda';
import { applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension';
import { observeDelta } from 'brookjs';

export { default as authorDelta } from './authorDelta';
export { default as repoDelta } from './repo';
export { default as commitsDelta } from './commitsDelta';
export { default as jobsDelta } from './jobsDelta';
export { default as routerDelta } from './routerDelta';
export { default as searchDelta } from './search';
export { default as siteDelta } from './site';
export { default as tinymcePluginDelta } from './tinymcePlugin';
export { default as userDelta } from './user';
export { default as webpackDelta } from './webpack';

export const applyDelta = R.pipe(observeDelta, applyMiddleware, composeWithDevTools);
