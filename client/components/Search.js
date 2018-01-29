// @flow
// @jsx h
import type { Observable } from 'kefir';
import type { Node } from 'react';
import type { SearchInputAction, SearchResultSelectionChangeAction } from '../types';
import './Search.scss';
import { h, Collector, loop, view } from 'brookjs-silt';
import i18n from '../helpers/i18n';
import { searchInputAction, searchResultSelectionChangeAction } from '../actions';

type ID = number | string;

type ResultsProps = {
    order : Array<ID>;
    dict : {
        [key : ID] : {
            filename : string;
        },
    };
};

export type SearchProps = {
    term: string;
    results: ResultsProps,
    loading: boolean
};

const onRadioChange = (evt$ : Observable<SyntheticInputEvent<*>>) : Observable<SearchResultSelectionChangeAction> => evt$
    .map((e : SyntheticInputEvent<*>) => searchResultSelectionChangeAction(e.target.value));

const HasResults = ({ stream$ } : { stream$ : Observable<ResultsProps>, }) => (
    <Collector>
        <ul className={'wpgp-search-results'}>
            {stream$.thru(loop((child$, id) => (
                <li className={'wpgp-search-result'} key={id}>
                    <input type="radio" id={`wpgp-radio-${id}`} value={id}
                        defaultChecked={false /* @todo use stream$ */}
                        name="wpgp-search"
                        onChange={onRadioChange}/>
                    <label htmlFor={`wpgp-radio-${id}`}
                        className="wpgp-search-result-title">
                        {child$.thru(view(child => child.filename))}
                    </label>
                </li>
            )))}
        </ul>
    </Collector>
);

const NoResults = () => (
    <p>{i18n('search.results.none')}</p>
);

const onSearchTyping = (evt$ : Observable<SyntheticInputEvent<*>>) : Observable<SearchInputAction> => evt$
    .map((e : SyntheticInputEvent<*>) : string => e.target.value)
    .debounce(300)
    .map(searchInputAction);

export const Search = ({ stream$ } : { stream$ : Observable<SearchProps>, }) : Node => (
    <Collector>
        <div className={'wpgp-search-container'}>
            <div className="wpgp-search-form">
                <label htmlFor="wpgp-search-field" className="wpgp-search-label">
                    {i18n('search.title')}
                </label>
                <input type="search" id="gistpen-search-field"
                    className="wpgp-search-field"
                    placeholder="keywords" defaultValue={stream$.take(1).map(props => props.term)}
                    onInput={onSearchTyping}/>
                {stream$.thru(view((props: SearchProps) => props.loading)).map((loading: boolean) => (
                    loading ? <div className={'loader'}>{i18n('search.loading')}</div> : null
                ))}
            </div>

            {stream$.thru(view(props => props.results)).map((results: ResultsProps) =>
                results.order.length ?
                    <HasResults stream$={stream$.thru(view(props => props.results))}/> :
                    <NoResults/>)}
        </div>
    </Collector>
);
