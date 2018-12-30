// @flow
// @jsx h
import type { Toggle, Loopable, ObservableProps } from '../../types';
import './Controls.scss';
import R from 'ramda';
import { toJunction, loop, view, h } from 'brookjs-silt';
import { i18n, link } from '../../helpers';
import { editorTabsToggle, editorThemeChange, editorInvisiblesToggle,
    editorWidthChange, editorStatusChange, editorSyncToggle,
    editorUpdateClick, editorAddClick } from '../../actions';

const mapCheckedToString : ((e: Event) => Toggle) = R.ifElse(
    R.path(['target', 'checked']),
    R.always('on'),
    R.always('off')
);

const getTargetValue : ((e: Event) => string) = R.path(['target', 'value']);

type Status = {
    slug: string,
    name: string,
    selected: boolean
};

type Theme = {
    slug: string,
    name: string,
    active: boolean
};

type Width = {
    slug: string,
    name: string,
    active: boolean
};

type Props = {
    statuses: Loopable<string, Status>,
    themes: Loopable<string, Theme>,
    widths: Loopable<string, Width>,
    gist: {
        show: boolean,
        url: ?string
    },
    sync: Toggle,
    tabs: Toggle,
    invisibles: Toggle,
    selectedTheme: string,
    selectedStatus: string
};

const toggleToBoolean = (toggle: Toggle): boolean => toggle === 'on';

const Controls = ({
    stream$,
    onStatusChange,
    onSyncChange,
    onThemeChange,
    onTabsChange,
    onWidthChange,
    onInvisiblesChange,
    onUpdateClick,
    onAddClick
}: ObservableProps<Props>) => (
    <div className={stream$.thru(view(props => `wpgp-editor-controls wpgp-editor-controls-${props.selectedTheme}`))}>
        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-editor-status">{i18n('editor.status')}: </label>
            <select id="wpgp-editor-status"
                value={stream$.thru(view(props => props.selectedStatus))}
                onChange={onStatusChange}>
                {stream$.thru(loop(props => props.statuses, (status$, key) => (
                    <option value={key} key={key}>
                        {status$}
                    </option>
                )))}
            </select>
        </div>

        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-editor-sync">{i18n('editor.sync')}</label>
            <input type="checkbox" id="wpgp-editor-sync"
                checked={stream$.thru(view(props => toggleToBoolean(props.sync)))}
                onChange={onSyncChange} />
        </div>

        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-editor-theme">{i18n('editor.theme')}: </label>
            <select id="wpgp-editor-theme"
                value={stream$.thru(view(props => props.selectedTheme))}
                onChange={onThemeChange}>
                {stream$.thru(loop(props => props.themes, (theme$, key) => (
                    <option value={key} key={key}>
                        {theme$}
                    </option>
                )))}
            </select>
        </div>

        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-enable-tabs">{i18n('editor.tabs')} </label>
            <input type="checkbox" id="wpgp-enable-tabs"
                checked={stream$.thru(view(props => toggleToBoolean(props.tabs)))}
                onChange={onTabsChange} />
        </div>

        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-editor-width">{i18n('editor.width')}: </label>
            <select id="wpgp-editor-width"
                value={stream$.thru(view(props => props.selectedWidth))}
                onChange={onWidthChange}>
                {stream$.thru(loop(props => props.widths, (width$, key) => (
                    <option value={key} key={key}>
                        {width$}
                    </option>
                )))}
            </select>
        </div>

        <div className="wpgp-editor-control">
            <label htmlFor="wpgp-enable-invisibles">{i18n('editor.invisibles')} </label>
            <input type="checkbox" id="wpgp-enable-invisibles"
                checked={stream$.thru(view(props => toggleToBoolean(props.invisibles)))}
                onChange={onInvisiblesChange} />
        </div>

        <div className="wpgp-editor-control">
            <button className="dashicons-before wpgp-button wpgp-button-update"
                onClick={onUpdateClick}>
                {i18n('editor.update')}
            </button>
            <button className="dashicons-before wpgp-button wpgp-button-add"
                onClick={onAddClick}>
                {i18n('editor.file.add')}
            </button>
            <a href={link('wpgp_route', 'commits')}
                className="dashicons-before wpgp-button wpgp-button-add">
                {i18n('editor.commits')}
            </a>
            {stream$.thru(view(props => props.gist)).map(gist => (
                gist.show ? (
                    <a href={gist.url}
                        className="dashicons-before wpgp-button wpgp-button-add">
                        {i18n('editor.gist')}
                    </a>
                ) : null
            ))}
        </div>
    </div>
);

export default toJunction({
    events: {
        onStatusChange: R.map(R.pipe(getTargetValue, editorStatusChange)),
        onSyncChange: R.map(R.pipe(mapCheckedToString, editorSyncToggle)),
        onThemeChange: R.map(R.pipe(getTargetValue, editorThemeChange)),
        onTabsChange: R.map(R.pipe(mapCheckedToString, editorTabsToggle)),
        onWidthChange: R.map(R.pipe(getTargetValue, editorWidthChange)),
        onInvisiblesChange: R.map(R.pipe(mapCheckedToString, editorInvisiblesToggle)),
        onUpdateClick: R.map(R.pipe(R.tap(e => e.preventDefault()), editorUpdateClick)),
        onAddClick: R.map(R.pipe(R.tap(e => e.preventDefault()), editorAddClick))
    }
})(Controls);
