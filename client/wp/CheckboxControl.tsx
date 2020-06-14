import { toJunction } from 'brookjs';
import { CheckboxControl } from '@wordpress/components';
import { Stream } from 'kefir';
import { checked } from './actions';

const events = {
  onChange: (e$: Stream<boolean, never>) => e$.map(checked),
};

export default toJunction(events)(CheckboxControl);
