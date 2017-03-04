// @flow
import type { Observable } from 'kefir';
import type { SearchInputAction } from '../../type';
import './index.scss';
import R from 'ramda';
import { component, events } from 'brookjs';
import { searchInputAction } from '../../action';

export default component({
    events: events({
        onSearchTyping: (evt$ : Observable<Event>) : Observable<SearchInputAction> => evt$
            .debounce(300)
            .map((R.pipe(
                R.path(['target', 'value']),
                searchInputAction
            ) : ((event : Event) => SearchInputAction)))
    })
});
