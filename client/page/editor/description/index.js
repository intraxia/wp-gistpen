import './index.scss';
import R from 'ramda';
import { component, events } from 'brookjs';
import { editorOptionsClickAction, editorDescriptionChangeAction } from '../../../action';

export default component({
    events: events({
        'oninput': R.map(R.pipe(R.path(['target', 'value']), editorDescriptionChangeAction)),
        'onclick': R.map(R.always(editorOptionsClickAction()))
    })
});
