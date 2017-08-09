// @flow
import R from 'ramda';

export { default as editor } from './editor';
export { default as gist } from './gist';
export { default as prism } from './prism';
export { default as repo } from './repo';
export { default as commits } from './commits';
export { default as route } from './route';
export { default as search } from './search';
export { default as jobs } from './jobs';
export { default as runs } from './runs';
export { default as messages } from './messages';

export const globals = R.pipe(R.defaultTo({}), R.identity);
