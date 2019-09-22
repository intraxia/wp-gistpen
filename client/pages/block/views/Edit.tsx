import React from 'react';
import { Nullable } from 'typescript-nullable';
import { TextControl, Button } from '@wordpress/components';
import { compose, withState } from '@wordpress/compose';

const Edit: React.FC<{
  className: string;
  id: Nullable<number>;
  setAttributes: (attr: { id: number }) => void;
  search: string;
  setState: (state: Partial<{ search: string }>) => void;
}> = ({ className, id, setAttributes, search, setState }) => {
  if (Nullable.isNone(id)) {
    return (
      <div className={className}>
        <h3>No snippet has been selected</h3>
        <TextControl
          label="Enter ID"
          value={search}
          onChange={search => setState({ search })}
        />
        <Button isPrimary onClick={() => setAttributes({ id: Number(search) })}>
          Set ID
        </Button>
      </div>
    );
  }

  return <div className={className}>id is {id}</div>;
};

export default compose(withState({ search: '' }))(Edit);
