import './Editor.scss';
import React, { useEffect } from 'react';
import { useDelta, RootJunction } from 'brookjs';
import Code from './Code';
import { reducer, initialState } from './state';
import { Toolbar } from './toolbar';
import { PrismLib } from './types';
import { editorTabsChange, editorWidthChange } from './actions';

export const Editor: React.FC<{
  Prism: PrismLib;
  toolbarItems?: React.ReactNode;
  language: string;
  tabs?: boolean;
  width?: number;
  initialCode?: string;
}> = ({
  Prism,
  toolbarItems = null,
  language,
  tabs,
  width,
  initialCode = initialState.code,
}) => {
  const { state, root$, dispatch } = useDelta(reducer, {
    ...initialState,
    code: initialCode,
  });

  useEffect(() => {
    if (tabs != null) {
      dispatch(editorTabsChange(tabs));
    }
  }, [tabs, dispatch]);

  useEffect(() => {
    if (width != null) {
      dispatch(editorWidthChange(width));
    }
  }, [width, dispatch]);

  return (
    <div className="code-toolbar">
      <Toolbar>{toolbarItems}</Toolbar>
      <RootJunction root$={root$}>
        <pre className={`language-${language} line-numbers`} spellCheck={false}>
          <Code
            Prism={Prism}
            language={language}
            code={state.code}
            cursor={state.cursor}
          />
        </pre>
      </RootJunction>
    </div>
  );
};
