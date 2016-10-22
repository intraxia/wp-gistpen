import './index.scss';
import { component } from 'brookjs';
import children from 'brookjs/children';
import ControlsComponent from './controls';
import DescriptionComponent from './description';
import BlobComponent from './blob';

export default component({
    children: children({
        'controls': ControlsComponent,
        'description': DescriptionComponent,
        'blobEditor': BlobComponent
    })
});
