// @flow
import type { Observable } from 'kefir';
import type { SearchInputAction, SearchResultSelectionChangeAction } from '../../type';
import './index.scss';
import R from 'ramda';
import { component, events, render } from 'brookjs';
import { searchInputAction, searchResultSelectionChangeAction } from '../../action';
import template from './index.hbs';

export default component({
    events: events({
        onSearchTyping: (evt$ : Observable<Event>) : Observable<SearchInputAction> => evt$
            .debounce(300)
            .map((R.pipe(
                R.path(['target', 'value']),
                searchInputAction
            ) : ((event : Event) => SearchInputAction))),
        onRadioChange: (evt$ : Observable<Event>) : Observable<SearchResultSelectionChangeAction> => evt$
            .map(R.pipe(
                R.path(['target', 'value']),
                searchResultSelectionChangeAction
            ))
    }),
    render: render(template)
});
