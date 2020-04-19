import React, { useEffect } from 'react';
import { useDelta, Delta, RootJunction } from 'brookjs';
import Kefir from 'kefir';
import { RootAction } from '../../util';
import { StatusMapper } from '../StatusMapper';
import { reducer, initialState, State, Attributes } from './state';

const rootDelta: Delta<RootAction, State> = () => Kefir.never();

export const Block: React.FC<
  Attributes & {
    className: string;
    setAttributes: (attributes: Partial<Attributes>) => void;
  }
> = ({ className, blobId, repoId, setAttributes }) => {
  const { state, root$ } = useDelta(
    reducer,
    initialState({ repoId, blobId }),
    rootDelta,
  );

  useEffect(() => {
    setAttributes({ repoId: state.repoId });
  }, [setAttributes, state.repoId]);

  useEffect(() => {
    setAttributes({ blobId: state.blobId });
  }, [setAttributes, state.blobId]);

  return (
    <RootJunction root$={root$}>
      <div className={className}>
        <StatusMapper
          status={state.status}
          elements={{
            'set-embed': () => (
              <div data-testid="set-embed">Create or choose</div>
            ),
            'edit-embed': () => <div data-testid="edit-embed">Edit blob</div>,
          }}
        />
      </div>
    </RootJunction>
  );
};
