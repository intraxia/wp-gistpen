import React from 'react';
import classNames from 'classnames';
import Code from './Code';
import { Props } from './types';

const propsToClassName = (prism: Props['prism']) =>
  classNames({
    gistpen: true,
    'line-numbers': prism['line-numbers'],
  });

const Blob: React.FC<Props> = props => (
  <pre
    className={propsToClassName(props.prism)}
    data-filename={props.blob.filename}
  >
    <Code {...props} />
  </pre>
);

export default Blob;
