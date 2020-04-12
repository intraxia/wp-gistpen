import React, { useEffect } from 'react';
import { useDelta, Delta, RootJunction, unreachable } from 'brookjs';
import Kefir from 'kefir';
import { RootAction } from '../../util';
import { reducer, initialState, State, Attributes } from './state';

const rootDelta: Delta<RootAction, State> = () => Kefir.never();

export const Block: React.FC<Attributes & {
  className: string;
  setAttributes: (attributes: Partial<Attributes>) => void;
}> = ({ className, blobId, repoId, setAttributes }) => {
  const { state, root$ } = useDelta(
    reducer,
    initialState({ repoId, blobId }),
    rootDelta
  );

  useEffect(() => {
    setAttributes({ repoId: state.repoId });
  }, [setAttributes, state.repoId]);

  useEffect(() => {
    setAttributes({ blobId: state.blobId });
  }, [setAttributes, state.blobId]);

  let children: JSX.Element;

  switch (state.status) {
    case 'set-embed':
      children = <div data-testid="set-embed">Create or choose</div>;
      break;
    case 'edit-embed':
      children = <div data-testid="edit-embed">Edit blob</div>;
      break;
    /* istanbul ignore next */
    default:
      return unreachable(state);
  }

  return (
    <RootJunction root$={root$}>
      <div className={className}>{children}</div>
    </RootJunction>
  );
};
