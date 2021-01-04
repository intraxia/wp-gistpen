import './Editor.scss';
import React, { useEffect } from 'react';
import { useDelta, RootJunction, toJunction } from 'brookjs';
import { ActionType } from 'typesafe-actions';
import Code from './Code';
import { reducer, initialState } from './state';
import { Toolbar } from './toolbar';
import { PrismLib } from './types';
import {
  editorTabsChange,
  editorWidthChange,
  editorStateChange,
} from './actions';

const Editor: React.FC<{
  className?: string;
  Prism: PrismLib;
  toolbarItems?: React.ReactNode;
  language: string;
  tabs?: boolean;
  width?: number;
  lineNumbers?: boolean;
  initialCode?: string;
  onStateChange: (action: ActionType<typeof editorStateChange>) => void;
}> = ({
  className,
  Prism,
  toolbarItems = null,
  language,
  tabs,
  width,
  lineNumbers = true,
  initialCode = initialState.code,
  onStateChange,
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

  useEffect(() => {
    onStateChange(editorStateChange(state.code, state.cursor));
  }, [state.code, state.cursor, onStateChange]);

  return (
    <div className={`code-toolbar ${className ?? ''}`.trim()}>
      <Toolbar>{toolbarItems}</Toolbar>
      <RootJunction root$={root$}>
        <Code
          Prism={Prism}
          language={language}
          lineNumbers={lineNumbers}
          code={state.code}
          cursor={state.cursor}
        />
      </RootJunction>
    </div>
  );
};

const events = {
  onStateChange: (e$: any) => e$,
};

export default toJunction(events)(Editor);
