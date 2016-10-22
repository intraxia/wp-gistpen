import './index.scss';
import R from 'ramda';
import component from 'brookjs/component';
import events from 'brookjs/events';
import { editorTabsToggleAction, editorThemeChangeAction, editorInvisiblesToggleAction,
    editorWidthChangeAction, repoStatusChangeAction, repoSyncToggleAction
} from '../../action';

const mapCheckedToString = R.ifElse(
    R.path(['target', 'checked']),
    R.always('on'),
    R.always('off')
);

const getTargetValue = R.path(['target', 'value']);

export default component({
    events: events({
        onStatusChange: R.map(
            R.pipe(getTargetValue, repoStatusChangeAction)
        ),
        onSyncToggle: R.map(
            R.pipe(mapCheckedToString, repoSyncToggleAction)
        ),
        onThemeChange: R.map(
            R.pipe(getTargetValue, editorThemeChangeAction)
        ),
        onTabsToggle: R.map(
            R.pipe(mapCheckedToString, editorTabsToggleAction)
        ),
        onWidthChange: R.map(
            R.pipe(getTargetValue, editorWidthChangeAction)
        ),
        onInvisiblesToggle: R.map(
            R.pipe(mapCheckedToString, editorInvisiblesToggleAction)
        ),
    })
});
