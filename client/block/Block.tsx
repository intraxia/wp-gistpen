import React, { useEffect } from 'react';
import { useDelta, Delta, RootJunction, unreachable } from 'brookjs';
import Kefir from 'kefir';
import { RootAction } from '../RootAction';
import { reducer, initialState, State, Attributes } from './state';
import SetEmbed from './SetEmbed';
import EditEmbed from './EditEmbed';

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

  let embed: JSX.Element;

  switch (state.status) {
    case 'set-embed':
      embed = <SetEmbed />;
      break;
    case 'edit-embed':
      embed = <EditEmbed repoId={state.repoId} blobId={state.blobId} />;
      break;
    default:
      return unreachable(state);
  }

  return (
    <RootJunction root$={root$}>
      <div className={className}>{embed}</div>
    </RootJunction>
  );
};
