import './index.scss';
import { component } from 'brookjs';
import children from 'brookjs/children';
import DescriptionComponent from './description';

export default component({
    children: children({
        'description': DescriptionComponent
    })
});
