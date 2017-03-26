// @flow
import R from 'ramda';
import { component, events, render } from 'brookjs';
import template from './accounts.hbs';
import { gistTokenChangeAction } from '../../action';

export default component({
    render: render(template),
    events: events({
        'onGistTokenChange': R.map(
            R.pipe(R.path(['target', 'value']), gistTokenChangeAction)
        )
    })
});
