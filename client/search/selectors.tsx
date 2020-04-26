import { State, HasSnippet, HasError } from './state';

export const isLoading = (state: State) =>
  state.status === 'searching' || state.status === 'researching';

export const hasSnippets = (state: State): state is HasSnippet =>
  state.status === 'found' ||
  state.status === 'researching' ||
  state.status === 'reerror';

export const hasError = (state: State): state is HasError =>
  state.status === 'error' || state.status === 'reerror';
