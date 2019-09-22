import React from 'react';
import { Nullable } from 'typescript-nullable';

const Save: React.FC<{ id: Nullable<number> }> = ({ id }) => {
  return id != null ? <>[gistpen id="{id}"]</> : null;
};

export default Save;
