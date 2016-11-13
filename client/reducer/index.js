import R from 'ramda';

export { default as editor } from './editor';
export { default as gist } from './gist';
export { default as prism } from './prism';
export { default as repo } from './repo';
export { default as route } from './route';

const defaultReducer = R.pipe(R.defaultTo({}), R.identity);

export const api = defaultReducer;
export const globals = defaultReducer;
