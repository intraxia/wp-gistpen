import React from 'react';
import { ofType, Maybe } from 'brookjs';
import {
  TextControl,
  ErrorNotice,
  WarningNotice,
  actions as wpActions,
} from '../wp';
import {
  searchResultSelectClick,
  searchResultSelectionChange,
  searchInput,
} from './actions';
import styles from './View.module.scss';
import SearchResult from './SearchResult';
import { ResultsPlaceholder } from './ResultsPlaceholder';

type ResultView = {
  id: number;
  label: string;
  render?: Maybe<React.ComponentProps<typeof SearchResult>['render']>;
};

export const View: React.FC<{
  disabled?: boolean;
  searchLabel: string;
  placeholderLabel: string;
  term: string;
  isLoading?: boolean;
  error?: Maybe<string>;
  results?: Maybe<ResultView[]>;
}> = ({
  disabled = false,
  searchLabel,
  placeholderLabel,
  term,
  isLoading,
  error,
  results,
}) => {
  return (
    <div data-testid="choosing">
      <div className={styles.search}>
        <TextControl
          className={styles.grow}
          label={searchLabel}
          value={term}
          data-testid="search-input"
          preplug={e$ =>
            e$
              .thru(ofType(wpActions.change))
              .map(a => searchInput(a.payload.value))
          }
        />
      </div>
      {error == null && results == null && !isLoading && (
        <WarningNotice>
          Type into the above search field to find a code snippet.
        </WarningNotice>
      )}
      {isLoading && <WarningNotice isLoading>Searching...</WarningNotice>}
      {error != null && (
        <ErrorNotice testid="error-notice">{error}</ErrorNotice>
      )}
      {results != null ? (
        results.length === 0 ? (
          <>
            <ErrorNotice>
              No results found for {term}. Try a different search term.
            </ErrorNotice>
            <ResultsPlaceholder label={placeholderLabel} />
          </>
        ) : (
          results.map(result => (
            <SearchResult
              key={result.id}
              label={result.label}
              render={result.render}
              disabled={disabled}
              preplug={e$ =>
                e$
                  .thru(ofType(searchResultSelectClick))
                  .map(() => searchResultSelectionChange(result.id))
              }
            />
          ))
        )
      ) : (
        <ResultsPlaceholder label={placeholderLabel} />
      )}
    </div>
  );
};
