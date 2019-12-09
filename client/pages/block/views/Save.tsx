import React from 'react';
import { Nullable } from 'typescript-nullable';

const Save: React.FC<{
  repoId: Nullable<number>;
  blobId: Nullable<number>;
}> = ({ blobId }) => {
  return blobId != null ? <>[gistpen id="{blobId}"]</> : null;
};

export default Save;
