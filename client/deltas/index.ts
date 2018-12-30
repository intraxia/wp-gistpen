import { applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension';
import { observeDelta } from 'brookjs';

export * from './authorDelta';

export const applyDelta = (...args: Array<any>) =>
  composeWithDevTools(applyMiddleware(observeDelta(...args)));
