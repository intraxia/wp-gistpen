import React from 'react';
import Blob from '../Blob';

type RepoProps = {
  blobs: Array<{
    ID: string;
    code: string;
    filename: string;
    language: string;
  }>;
  prism: {
    theme: string;
    'line-numbers': boolean;
    'show-invisibles': boolean;
  };
};

const Repo: React.FC<RepoProps> = ({ blobs, prism }) => (
  <div>
    {blobs.map(blob => (
      <Blob key={blob.ID} blob={blob} prism={prism} />
    ))}
  </div>
);

export default Repo;
