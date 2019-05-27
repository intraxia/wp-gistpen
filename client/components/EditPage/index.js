// @flow
// @jsx h
import type { Action, EditorPageProps, ObservableProps } from '../../types';
import './index.scss';
import { h, loop } from 'brookjs-silt';
import Controls from './Controls';
import Description from './Description';
import Instance from './Instance';

const Editor = ({ stream$ }: ObservableProps<EditorPageProps>) => {
    const controls$ = stream$.map((props: EditorPageProps) => ({
        statuses: {
            order: Object.keys(props.globals.statuses),
            dict: props.globals.statuses
        },
        themes: {
            order: Object.keys(props.globals.themes),
            dict: props.globals.themes
        },
        widths: {
            order: props.globals.ace_widths,
            dict: props.globals.ace_widths
                .reduce((dict, width) =>
                    Object.assign(dict, { [width]: width }), {})
        },
        gist: {
            show: props.repo.gist_url != null,
            url: props.repo.gist_url
        },
        sync: props.editor.sync,
        tabs: props.editor.tabs,
        invisibles: props.editor.invisibles,
        selectedTheme: props.editor.theme,
        selectedStatus: props.editor.status,
        selectedWidth: props.editor.width,
    }));

    const description$ = stream$.map((props: EditorPageProps) => ({
        description: props.editor.description,
        loading: props.ajax.running
    }));

    return (
        <div data-brk-container="editor" className="wpgp-editor">
            <div className="wpgp-editor-row">
                <Description stream$={description$} />
            </div>

            <div className="wpgp-editor-row">
                <Controls stream$={controls$} />
            </div>

            {stream$.thru(loop((props: EditorPageProps) => props.editor.instances, (instance$, key) => (
                <div className="wpgp-editor-row" key={key}>
                    <Instance
                        stream$={instance$}
                        preplug={instance$ => instance$.spy().map((action: Action) => ({
                            ...action,
                            meta: { key }
                        }))}
                    />
                </div>
            )))}
        </div>
    );
};

export default Editor;
