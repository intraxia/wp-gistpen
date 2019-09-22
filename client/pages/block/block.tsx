import * as React from 'react';
import { Nullable } from 'typescript-nullable';
import { Block } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { Edit, Save } from './views';

type Module = Block<{ id: Nullable<number> }>;

export const title: Module['title'] = __('Gistpen Code Snippet');

export const icon: Module['icon'] = { src: 'editor-code' };

export const category: Module['category'] = 'widgets';

export const keywords: Module['keywords'] = [
  __('code'),
  __('snippet'),
  __('gistpen')
];

export const attributes: Module['attributes'] = {
  id: {
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
      id={attributes.id}
      setAttributes={setAttributes}
    />
  );
};

export const save: Module['save'] = ({ attributes }) => {
  return <Save id={attributes.id} />;
};
