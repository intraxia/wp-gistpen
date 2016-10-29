import './index.scss';
import R from 'ramda';
import { component } from 'brookjs';
import children from 'brookjs/children';
import ControlsComponent from './controls';
import DescriptionComponent from './description';
import InstanceComponent from './instance';

export default component({
    children: children({
        'controls': ControlsComponent,
        'description': DescriptionComponent,
        'instance': {
            factory: InstanceComponent,
            key: 'key',
            modifyChildProps: R.map(props => props.editor.instances.map(instance => Object.assign({}, instance, { editor: props.editor })))
        }
    })
});
