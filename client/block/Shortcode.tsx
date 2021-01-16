import { Maybe } from 'brookjs';
import React from 'react';

const maybeAttribute = (value: Maybe<string | number>, attribute: string) =>
  value != null ? ` ${attribute}="${value}"` : '';

export const Shortcode: React.FC<{
  blobId: number;
  highlight?: Maybe<string>;
  offset?: Maybe<number>;
}> = ({ blobId, highlight, offset }) => (
  <>
    [gistpen id="{blobId}"{maybeAttribute(highlight, 'highlight')}
    {maybeAttribute(offset, 'offset')}]
  </>
);
