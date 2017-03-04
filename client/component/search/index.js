// @flow
import type { Observable } from 'kefir';
import type { SearchInputAction } from '../../type';
import './index.scss';
import R from 'ramda';
import { component, events, render } from 'brookjs';
import { searchInputAction } from '../../action';
import template from './index.hbs';

export default component({
    events: events({
        onSearchTyping: (evt$ : Observable<Event>) : Observable<SearchInputAction> => evt$
            .debounce(300)
            .map((R.pipe(
                R.path(['target', 'value']),
                searchInputAction
            ) : ((event : Event) => SearchInputAction)))
    }),
    render: render(template)
});
