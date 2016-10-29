import './index.scss';
import R from 'ramda';
import { component } from 'brookjs';
import children from 'brookjs/children';
import ControlsComponent from './controls';
import DescriptionComponent from './description';
import BlobComponent from './blob';

export default component({
    children: children({
        'controls': ControlsComponent,
        'description': DescriptionComponent,
        'blobEditor': {
            factory: BlobComponent,
            modifyChildProps: R.map(props => props.repo.blobs.map(blob => Object.assign({}, blob, { editor: props.editor })))
        }
    })
});
