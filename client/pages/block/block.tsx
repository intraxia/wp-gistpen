import * as React from 'react';
import { Block } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Edit, Save } from './views';

type Module = Block<
  { blobId: number; repoId: number } | { blobId: null; repoId: null }
>;

export const title: Module['title'] = __('Gistpen Code Snippet');

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
    <Edit
      className={className}
      repoId={attributes.repoId}
      blobId={attributes.blobId}
      setIds={(repoId, blobId) => setAttributes({ repoId, blobId })}
    />
  );
};

export const save: Module['save'] = ({ attributes }) => {
  return <Save {...attributes} />;
};
