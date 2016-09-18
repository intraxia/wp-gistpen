import R from 'ramda';
import component from 'brookjs/component';
import render from 'brookjs/render';
import events from 'brookjs/events';
import template from './accounts.hbs';
import { gistTokenChangeAction } from '../action';

export default component({
    render: render(template),
    events: events({
        'onGistTokenChange': R.map(
            R.pipe(R.path(['target', 'value']), gistTokenChangeAction)
        )
    })
});
