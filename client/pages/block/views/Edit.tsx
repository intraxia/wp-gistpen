import React from 'react';
import { Nullable } from 'typescript-nullable';
import Editor from './Editor';
import Selector from './Selector';

const Edit: React.FC<{
  className: string;
  blobId: Nullable<number>;
  repoId: Nullable<number>;
  setIds: (repoId: number, blobId: number) => void;
}> = ({ className, blobId, repoId, setIds }) => {
  return (
    <div className={className}>
      {Nullable.isNone(blobId) || Nullable.isNone(repoId) ? (
        <Selector setIds={setIds} />
      ) : (
        <Editor blobId={blobId} repoId={repoId} />
      )}
    </div>
  );
};

export default Edit;
