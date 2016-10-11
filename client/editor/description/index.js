import './index.scss';
import R from 'ramda';
import component from 'brookjs/component';
import events from 'brookjs/events';

export default component({
    events: events({
        'oninput': R.map(
            R.pipe(
                R.path(['target', 'value']),
                value => ({
                    type: 'REPO_DESCRIPTION_CHANGE',
                    payload: { value }
                })
            )
        ),
        'onclick': R.map(R.always({ type: 'OPTIONS_CLICK' }))
    })
});
