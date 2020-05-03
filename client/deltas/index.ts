import { applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension/developmentOnly';
import { observeDelta, Delta } from 'brookjs';

export * from './authorDelta';
export * from './commitsDelta';
export * from './jobsDelta';
export * from './repoDelta';
export * from './routerDelta';
export * from './searchDelta';
export * from './siteDelta';
export * from './tinymcePlugin';
export * from './userDelta';
export * from './webpackDelta';

export const applyDelta = <A extends { type: string }, S>(
  ...args: Delta<A, S>[]
) =>
  composeWithDevTools({
    serialize: {
      options: {
        error: true,
      },
    },
  } as object)(applyMiddleware(observeDelta(...args)));
