import { Observable } from 'kefir';
import React from 'react';
import './index.scss';
import { toJunction } from 'brookjs-silt';
import { i18n } from '../../helpers';
import { searchInput, searchResultSelectionChange } from '../../actions';
import Loader from '../Loader';

type Results = Array<{
  id: string;
  filename: string;
}>;

type Props = {
  onSearchTyping: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onRadioChange: (id: string) => void;
  term: string;
  results: Results;
  loading: boolean;
};

const HasResults: React.FC<{
  results: Results;
  onRadioChange: (id: string) => void;
}> = ({ results, onRadioChange }) => (
  <ul className={'wpgp-search-results'}>
    {results.map(({ id, filename }) => (
      <li className={'wpgp-search-result'} key={id}>
        <input
          type="radio"
          id={`wpgp-radio-${id}`}
          value={id}
          defaultChecked={false}
          name="wpgp-search"
          onChange={() => onRadioChange(id)}
        />
        <label
          htmlFor={`wpgp-radio-${id}`}
          className="wpgp-search-result-title"
        >
          {filename}
        </label>
      </li>
    ))}
  </ul>
);

const NoResults: React.FC<{ term: string }> = ({ term }) => (
  <p>{i18n('search.results.no', term)}</p>
);

const NoTerm = () => <p>{i18n('search.term.no')}</p>;

const Search: React.FC<Props> = ({
  term,
  loading,
  results,
  onSearchTyping,
  onRadioChange,
}) => (
  <div className={'wpgp-search-container'}>
    <div className="wpgp-search-form">
      <label htmlFor="wpgp-search-field" className="wpgp-search-label">
        {i18n('search.title')}
      </label>
      <input
        type="search"
        id="gistpen-search-field"
        className="wpgp-search-field"
        placeholder="keywords"
        defaultValue={term}
        onChange={onSearchTyping}
      />
      {loading ? <Loader text={i18n('search.loading')} /> : null}
    </div>

    {term ? (
      results.length ? (
        <HasResults results={results} onRadioChange={onRadioChange} />
      ) : (
        <NoResults term={term} />
      )
    ) : (
      <NoTerm />
    )}
  </div>
);

const events = {
  onSearchTyping: (
    evt$: Observable<React.ChangeEvent<HTMLInputElement>, never>,
  ) =>
    evt$
      .map(e => e.target.value)
      .debounce(300)
      .map(searchInput),
  onRadioChange: (evt$: Observable<string, never>) =>
    evt$.map(searchResultSelectionChange),
};

export default toJunction(events)(Search);
