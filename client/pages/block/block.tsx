import React from 'react';
import { Block } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Maybe } from 'brookjs';
import { Block as Gutenblock, Shortcode } from '../../components';

export type Attributes = {
  blobId: Maybe<number>;
  repoId: Maybe<number>;
};

type Module = Block<Attributes>;

export const title: Module['title'] = __('Gistpen Code Snippet', 'wp-gistpen');

export const icon: Module['icon'] = { src: 'editor-code' };

export const category: Module['category'] = 'widgets';

export const keywords: Module['keywords'] = [
  __('code'),
  __('snippet'),
  __('gistpen')
];

export const attributes: Module['attributes'] = {
  repoId: {
    type: 'number'
  },
  blobId: {
    type: 'number'
  }
};

export const edit: Module['edit'] = ({
  attributes,
  className,
  setAttributes
}) => {
  return (
    <Gutenblock
      className={className}
      repoId={attributes.repoId}
      blobId={attributes.blobId}
      setAttributes={setAttributes}
    />
  );
};

export const save: Module['save'] = ({ attributes: { blobId } }) =>
  blobId != null ? <Shortcode blobId={blobId} /> : null;
