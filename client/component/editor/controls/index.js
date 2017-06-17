// @flow
import type { Toggle } from '../../../type';
import './index.scss';
import R from 'ramda';
import { component, events, render } from 'brookjs';
import { editorTabsToggleAction, editorThemeChangeAction, editorInvisiblesToggleAction,
    editorWidthChangeAction, editorStatusChangeAction, editorSyncToggleAction,
    editorUpdateClickAction, editorAddClickAction } from '../../../action';
import template from './index.hbs';

const mapCheckedToString : ((e : Event) => Toggle) = R.ifElse(
    R.path(['target', 'checked']),
    R.always('on'),
    R.always('off')
);

const getTargetValue : ((e : Event) => string) = R.path(['target', 'value']);

export default component({
    render: render(template),
    events: events({
        onStatusChange: R.map(
            R.pipe(getTargetValue, editorStatusChangeAction)
        ),
        onSyncToggle: R.map(
            R.pipe(mapCheckedToString, editorSyncToggleAction)
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
        onUpdateClick: R.map(editorUpdateClickAction),
        onAddClick: R.map(editorAddClickAction)
    })
});
