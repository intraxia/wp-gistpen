import React from 'react';
import { RootJunction, useDelta, toJunction, ofType } from 'brookjs';
import { useGlobals } from '../../../globals';
import { newBlobAttached, newRepoCreated } from '../../actions';
import { View } from './View';
import { reducer, initialState } from './state';
import { rootDelta } from './delta';

const Creating: React.FC = () => {
  const globals = useGlobals();
  const { state, root$ } = useDelta(
    reducer,
    { ...initialState, globals },
    rootDelta,
  );

  return (
    <RootJunction root$={root$}>
      <View {...state} />
    </RootJunction>
  );
};

export default toJunction(a$ =>
  a$.thru(ofType(newBlobAttached, newRepoCreated)),
)(Creating);
