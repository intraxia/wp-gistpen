// @flow
import type { Observable } from 'kefir';
import { component, events } from 'brookjs';
import { jobStartClick } from '../../action';

export default component({
    events: events({
        onStartClick: (evt$ : Observable<Event>) => evt$
            .map(jobStartClick)
            .debounce(200)
    })
});
