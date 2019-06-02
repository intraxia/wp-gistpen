import React from 'react';
import { prismSlug } from '../../helpers';

const Pre: React.FC<{ language: string }> = ({ language, children }) => (
  <pre
    className={`language-${prismSlug(language)} line-numbers`}
    spellCheck={false}
  >
    {children}
  </pre>
);

export default Pre;
