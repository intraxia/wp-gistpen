import React from 'react';
import { ofType, useDelta, RootJunction, toJunction, Delta } from 'brookjs';
import Kefir, { Observable } from 'kefir';
import { RootAction } from '../util';
import { snippetSelected } from './actions';
import { View } from './View';
import { reducer, initialState, State } from './state';
import { searchDelta } from './delta';
import { useGlobals, usePrismConfig } from './context';
import { isLoading, hasError, hasSnippets } from './selectors';

const rootDelta: Delta<RootAction, State> = (action$, state$) => {
  const search$ = searchDelta(
    action$,
    state$.map(state => ({
      root: state.globals.root,
      nonce: state.globals.nonce,
      term: state.term,
    })),
  );

  return Kefir.merge([search$]);
};

const Choosing = () => {
  const globals = useGlobals();
  const prism = usePrismConfig();
  const { state, root$ } = useDelta(
    reducer,
    { ...initialState, globals },
    rootDelta,
  );
  return (
    <RootJunction root$={root$}>
      <View
        placeholderLabel="placeholder.js"
        term={state.term}
        isLoading={isLoading(state)}
        error={hasError(state) ? state.error : null}
        results={
          hasSnippets(state)
            ? state.snippets.map(snippet => ({
                id: snippet.ID,
                label: snippet.filename,
                render: {
                  blob: {
                    filename: snippet.filename,
                    code: snippet.code,
                    language: snippet.language.slug,
                  },
                  prism,
                },
              }))
            : null
        }
      />
    </RootJunction>
  );
};

const events = {};

const combiner = (action$: Observable<RootAction, never>) =>
  action$.thru(ofType(snippetSelected));

export default toJunction(events, combiner)(Choosing);
