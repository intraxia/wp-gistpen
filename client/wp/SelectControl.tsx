import { toJunction } from 'brookjs';
import { SelectControl } from '@wordpress/components';
import { Stream } from 'kefir';
import { change } from './actions';

const events = {
  onChange: (e$: Stream<string, never>) => e$.map(change),
};

export default toJunction(events)(SelectControl);
