import React from 'react';

export const Shortcode: React.FC<{
  blobId: number;
}> = ({ blobId }) => <>[gistpen id="{blobId}"]</>;
