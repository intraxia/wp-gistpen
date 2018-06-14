// @flow
// @jsx h
import type { Observable } from 'kefir';
import type { Action, EditorPageProps, EditorInstance } from '../../types';
import './index.scss';
import { component, children, render } from 'brookjs';
import { fromReact } from 'brookjs-silt';
import Controls from './Controls';
import Description from './Description';
import type { Props as EditorInstanceProps } from './instance/types';
import Instance from './instance';
import template from './index.hbs';

export default component({
    children: children({
        'controls': {
            factory: fromReact(Controls),
            modifyChildProps: props$ => props$.map((props: EditorPageProps) => ({
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
            }))
        },
        'description': {
            factory: fromReact(Description),
            modifyChildProps: props$ => props$.map((props: EditorPageProps) => ({
                description: props.editor.description,
                loading: props.ajax.running
            }))
        },
        'instance': {
            factory: fromReact(Instance),
            modifyChildProps: (props$: Observable<EditorPageProps>, key: string): Observable<EditorInstanceProps> =>
                props$.map((props: EditorPageProps): EditorInstanceProps => {
                    const instance: EditorInstance = props.editor.instances.find((instance: EditorInstance) => instance.key === key) || {
                        key,
                        code: '\n',
                        cursor: false,
                        filename: '',
                        history: {
                            undo: [],
                            redo: []
                        },
                        language: 'plaintext'
                    };

                    return {
                        filename: instance.filename,
                        code: instance.code,
                        invisibles: props.editor.invisibles,
                        language: instance.language,
                        theme: props.editor.theme,
                        cursor: instance.cursor,
                        languages: {
                            order: Object.keys(props.globals.languages),
                            dict: props.globals.languages
                        },
                    };
                }),
            preplug: (instance$: Observable<Action>, key: string) => instance$.map((action: Action) => ({
                ...action,
                meta: { key }
            }))
        }
    }),
    render: render(template)
});
