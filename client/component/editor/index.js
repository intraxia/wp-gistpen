// @flow
import type { Observable } from 'kefir';
import type { Action, EditorPageProps, EditorInstance, EditorInstanceProps } from '../../type';
import './index.scss';
import { component, children, render } from 'brookjs';
import ControlsComponent from './controls';
import DescriptionComponent from './description';
import InstanceComponent from './instance';
import template from './index.hbs';

export default component({
    children: children({
        'controls': ControlsComponent,
        'description': DescriptionComponent,
        'instance': {
            factory: InstanceComponent,
            modifyChildProps: (props$ : Observable<EditorPageProps>, key : string) : Observable<EditorInstanceProps> => {
                return props$.flatten((props : EditorPageProps) : Array<EditorInstanceProps> => {
                    const instance = props.editor.instances.find((instance : EditorInstance) => instance.key === key);

                    if (!instance) {
                        return [];
                    }

                    return [{
                        instance: {
                            ...instance,
                            code: !/\n$/.test(instance.code) ? instance.code + '\n' : instance.code
                        },
                        editor: props.editor
                    }];
                });
            },
            preplug: (instance$ : Observable<Action>, key : string) => instance$.map((action : Action) => ({
                ...action,
                meta: { key }
            }))
        }
    }),
    render: render(template)
});
