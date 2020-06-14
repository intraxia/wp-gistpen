import { toJunction } from 'brookjs';
import { Button } from '@wordpress/components';
import { Stream } from 'kefir';
import { click } from './actions';

const events = {
  onClick: (e$: Stream<void, never>) => e$.map(click),
};

export default toJunction(events)(Button);
