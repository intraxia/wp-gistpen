import './index.scss';
import R from 'ramda';
import { component } from 'brookjs';
import children from 'brookjs/children';
import render from 'brookjs/render';
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
            key: 'instance.key',
            modifyChildProps: R.map(props => props.editor.instances.map(instance => ({ ...props, instance })))
        }
    }),
    render: render(template)
});
