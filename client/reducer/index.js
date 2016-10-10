import R from 'ramda';

export { default as gist } from './gist';
export { default as prism } from './prism';
export { default as route } from './route';

const defaultReducer = R.pipe(R.defaultTo({}), R.identity);

export const globals = defaultReducer;
