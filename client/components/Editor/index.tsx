import './index.scss';
import React, { memo, useRef, useEffect } from 'react';
import { Props } from './types';
import { toJunction } from 'brookjs-silt';
import toolbarStyles from 'prismjs/plugins/toolbar/prism-toolbar.css';
import {
  editorFilenameChange,
  editorDeleteClick,
  editorLanguageChange
} from '../../actions';
import { i18n } from '../../helpers';
import Pre from './Pre';
import Code from './Code';
import { Observable } from 'kefir';

toolbarStyles.use();

const ToolbarButton: React.FC<{ children: React.ReactNode }> = ({
  children
}) => <div className="toolbar-item">{children}</div>;

const _Filename: React.FC<{
  filename: string;
  onInput: (e: React.ChangeEvent<HTMLSpanElement>) => void;
}> = ({ filename, onInput }) => {
  const ref = useRef<HTMLSpanElement>(null);

  useEffect(() => {
    if (ref.current != null) {
      ref.current.textContent = filename;
    }
  }, [ref.current]);

  return (
    <ToolbarButton>
      <span ref={ref} contentEditable spellCheck={false} onInput={onInput} />
    </ToolbarButton>
  );
};

const Filename = memo(_Filename, () => true);

const Language: React.FC<{
  language: string;
  languages: Array<{
    value: string;
    label: string;
  }>;
  onChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
}> = ({ language, languages, onChange }) => (
  <ToolbarButton>
    <select onChange={onChange} value={language}>
      {languages.map(lang => (
        <option value={lang.value} key={lang.value}>
          {lang.label}
        </option>
      ))}
    </select>
  </ToolbarButton>
);

const Delete: React.FC<{
  onClick: (e: React.MouseEvent<HTMLButtonElement>) => void;
}> = ({ onClick }) => (
  <ToolbarButton>
    <button type="button" onClick={onClick}>
      {i18n('editor.delete')}
    </button>
  </ToolbarButton>
);

const Toolbar: React.FC<{ children: React.ReactNode }> = ({ children }) => (
  <div className="toolbar">{children}</div>
);

const Editor: React.FC<Props> = ({
  filename,
  code,
  language,
  languages,
  invisibles,
  cursor,
  theme,
  onFilenameChange,
  onLanguageChange,
  onDeleteClick
}) => (
  <div className="editor page">
    <div className="code-toolbar">
      <Toolbar>
        <Filename filename={filename} onInput={onFilenameChange} />
        <Language
          language={language}
          languages={languages}
          onChange={onLanguageChange}
        />
        <Delete onClick={onDeleteClick} />
      </Toolbar>
      <Pre language={language}>
        <Code
          code={code}
          language={language}
          invisibles={invisibles}
          cursor={cursor}
          theme={theme}
        />
      </Pre>
    </div>
  </div>
);

const events = {
  onFilenameChange: (
    evt$: Observable<React.ChangeEvent<HTMLSpanElement>, Error>
  ) => evt$.map(e => editorFilenameChange(e.target.textContent || '', null)),
  onLanguageChange: (
    evt$: Observable<React.ChangeEvent<HTMLSelectElement>, Error>
  ) => evt$.map(e => editorLanguageChange(e.target.value || '', null)),
  onDeleteClick: (
    evt$: Observable<React.MouseEvent<HTMLButtonElement>, Error>
  ) => evt$.map(() => editorDeleteClick(null))
};

export default toJunction(events)(Editor);
