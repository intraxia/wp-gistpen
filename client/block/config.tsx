import React from 'react';
import { Block as BlockConfig } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Shortcode } from '../wp';
import { GlobalsProvider } from '../globals';
import { Attributes } from './state';
import { Block } from './Block';

type Config = BlockConfig<Attributes>;

export const title: Config['title'] = __('Gistpen Code Snippet', 'wp-gistpen');

export const description: Config['description'] = __(
  'Add or edit a code snippet to insert into your post.',
  'wp-gistpen',
);

export const icon: Config['icon'] = { src: 'editor-code' };

export const category: Config['category'] = 'widgets';

export const keywords: Config['keywords'] = [
  __('code'),
  __('snippet'),
  __('gistpen'),
];

export const attributes: Config['attributes'] = {
  repoId: {
    type: 'number',
  },
  blobId: {
    type: 'number',
  },
};

export const edit: Config['edit'] = ({
  attributes,
  className,
  setAttributes,
}) => {
  return (
    <GlobalsProvider value={window.__GISTPEN_GLOBALS__}>
      <Block
        className={className}
        repoId={attributes.repoId}
        blobId={attributes.blobId}
        setAttributes={setAttributes}
      />
    </GlobalsProvider>
  );
};

export const save: Config['save'] = ({ attributes: { blobId } }) =>
  blobId != null ? <Shortcode blobId={blobId} /> : null;
