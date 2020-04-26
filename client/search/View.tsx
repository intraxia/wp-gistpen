import React from 'react';
import { Spinner, Notice } from '@wordpress/components';
import { ofType } from 'brookjs';
import { TextControl } from '../components';
import { PrismState } from '../reducers';
import { change } from '../actions';
import {
  searchResultSelectClick,
  searchResultSelectionChange,
  searchInput,
} from './actions';
import styles from './View.module.scss';
import SearchResult from './SearchResult';
import { isLoading, hasError, hasSnippets } from './selectors';
import { State } from './state';

const SnippetsPlaceholder: React.FC = () => {
  return (
    <>
      <SearchResult filename="placeholder.js" />
      <SearchResult filename="placeholder.js" />
      <SearchResult filename="placeholder.js" />
      <SearchResult filename="placeholder.js" />
      <SearchResult filename="placeholder.js" />
    </>
  );
};

export const View: React.FC<
  State & {
    prism: PrismState;
  }
> = ({ prism, ...state }) => {
  return (
    <div data-testid="choosing">
      <div className={styles.search}>
        <TextControl
          className={styles.grow}
          label="Search for snippet"
          value={state.term}
          preplug={e$ =>
            e$.thru(ofType(change)).map(a => searchInput(a.payload.value))
          }
        />
      </div>
      {state.status === 'initial' && (
        <Notice
          status="warning"
          isDismissible={false}
          className={styles.notice}
        >
          Type into the above search field to find a code snippet.
        </Notice>
      )}
      {isLoading(state) && (
        <Notice
          status="warning"
          isDismissible={false}
          className={styles.notice}
        >
          <Spinner /> <span>Searching...</span>
        </Notice>
      )}
      {hasError(state) && (
        <Notice
          status="error"
          isDismissible={false}
          className={styles.notice}
          data-testid="error-notice"
        >
          {state.error}
        </Notice>
      )}
      {hasSnippets(state) ? (
        state.snippets.length === 0 ? (
          <>
            <Notice
              status="error"
              isDismissible={false}
              className={styles.notice}
            >
              No results found for {state.term}. Try a different term
            </Notice>
            <SnippetsPlaceholder />
          </>
        ) : (
          state.snippets.map(snippet => (
            <SearchResult
              key={snippet.ID}
              filename={snippet.filename}
              render={{
                id: snippet.ID,
                filename: snippet.filename,
                blob: {
                  code: snippet.code,
                  language: snippet.language.slug,
                },
                prism,
              }}
              preplug={e$ =>
                e$
                  .thru(ofType(searchResultSelectClick))
                  .map(() => searchResultSelectionChange(snippet.ID))
              }
            />
          ))
        )
      ) : (
        <SnippetsPlaceholder />
      )}
    </div>
  );
};
