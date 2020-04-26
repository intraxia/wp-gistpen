import React from 'react';
import { Spinner, Notice } from '@wordpress/components';
import { ofType, Maybe } from 'brookjs';
import { TextControl } from '../components';
import { change } from '../actions';
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
  render: React.ComponentProps<typeof SearchResult>['render'];
};

export const View: React.FC<{
  placeholderLabel: string;
  term: string;
  isLoading?: boolean;
  error?: Maybe<string>;
  results?: Maybe<ResultView[]>;
}> = ({ placeholderLabel, term, isLoading, error, results }) => {
  return (
    <div data-testid="choosing">
      <div className={styles.search}>
        <TextControl
          className={styles.grow}
          label="Search for snippet"
          value={term}
          preplug={e$ =>
            e$.thru(ofType(change)).map(a => searchInput(a.payload.value))
          }
        />
      </div>
      {error == null && results == null && !isLoading && (
        <Notice
          status="warning"
          isDismissible={false}
          className={styles.notice}
        >
          Type into the above search field to find a code snippet.
        </Notice>
      )}
      {isLoading && (
        <Notice
          status="warning"
          isDismissible={false}
          className={styles.notice}
        >
          <Spinner /> <span>Searching...</span>
        </Notice>
      )}
      {error != null && (
        <Notice
          status="error"
          isDismissible={false}
          className={styles.notice}
          data-testid="error-notice"
        >
          {error}
        </Notice>
      )}
      {results != null ? (
        results.length === 0 ? (
          <>
            <Notice
              status="error"
              isDismissible={false}
              className={styles.notice}
            >
              No results found for {term}. Try a different term
            </Notice>
            <ResultsPlaceholder label={placeholderLabel} />
          </>
        ) : (
          results.map(result => (
            <SearchResult
              key={result.id}
              label={result.label}
              render={result.render}
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
