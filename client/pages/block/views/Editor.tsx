import React from 'react';

const Editor: React.FC<{ repoId: number; blobId: number }> = ({
  repoId,
  blobId
}) => {
  return (
    <div>
      <p>Repo ID: {repoId}.</p>
      <p>Blob ID: {blobId}</p>
    </div>
  );
};

export default Editor;
