import React, { useState } from 'react';
import Prism from 'prismjs';
import Editor from '../Editor';
import { ToolbarSelect, ToolbarInput } from '../toolbar';
import { setTheme, togglePlugin } from '../../prism';

setTheme('twilight');
togglePlugin('toolbar', true);

export default {
  title: 'Prism Editor',
};

export const Basic = () => <Editor Prism={Prism} language="js" />;

export const WithWidth = () => <Editor Prism={Prism} language="js" width={4} />;

export const WithTabs = () => {
  return <Editor Prism={Prism} language="js" tabs />;
};

const options = [
  { value: 'yes', label: 'Yes' },
  { value: 'no', label: 'No' },
] as const;

const StatefulEditor = () => {
  const [input, setInput] = useState('');
  const [select, setSelect] = useState<'yes' | 'no'>('yes');

  return (
    <Editor
      Prism={Prism}
      language="js"
      toolbarItems={
        <>
          <ToolbarInput value={input} label={'Input'} onChange={setInput} />
          <ToolbarSelect
            name="yes-or-no"
            label="Yes or no?"
            value={select}
            options={options}
            onChange={setSelect}
          />
        </>
      }
    />
  );
};

export const WithToolbar = () => <StatefulEditor />;
