// @flow
// @jsx h
import type { Observable } from 'kefir';
import type { Action, EditorPageProps, EditorInstance, EditorInstanceProps } from '../../types';
import './index.scss';
import { component, children, render } from 'brookjs';
import { fromReact } from 'brookjs-silt';
import Controls from './Controls';
import Description from './Description';
import InstanceComponent from './instance';
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
            modifyChildProps: props$ => props$.map(props => ({ description: props.editor.description }))
        },
        'instance': {
            factory: InstanceComponent,
            modifyChildProps: (props$: Observable<EditorPageProps>, key: string): Observable<EditorInstanceProps> => {
                return props$.map((props: EditorPageProps): EditorInstanceProps => {
                    const instance = props.editor.instances.find((instance: EditorInstance) => instance.key === key);

                    if (instance == null) {
                        return {
                            instance: {
                                key,
                                code: '\n',
                                cursor: false,
                                filename: '',
                                history: {
                                    undo: [],
                                    redo: []
                                },
                                language: 'plaintext'
                            },
                            editor: props.editor
                        };
                    }

                    return {
                        instance: {
                            ...instance,
                            code: !/\n$/.test(instance.code) ? instance.code + '\n' : instance.code
                        },
                        editor: props.editor
                    };
                });
            },
            preplug: (instance$: Observable<Action>, key: string) => instance$.map((action: Action) => ({
                ...action,
                meta: { key }
            }))
        }
    }),
    render: render(template)
});
