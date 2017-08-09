// @flow
import type { Observable } from 'kefir';
import { component, events } from 'brookjs';
import { jobDispatchClick } from '../../action';

export default component({
    events: events({
        onStartClick: (evt$ : Observable<Event>) => evt$
            .map(jobDispatchClick)
            .debounce(200)
    })
});
