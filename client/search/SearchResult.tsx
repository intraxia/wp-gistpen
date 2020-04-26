import React, { useCallback, useState } from 'react';
import { Placeholder, Button, Popover } from '@wordpress/components';
import { toJunction } from 'brookjs';
import { Observable } from 'kefir';
import { Blob } from '../components';
import { searchResultSelectClick } from './actions';
import styles from './SearchResult.module.scss';

type Render = {
  id: number;
  filename: string;
  blob: {
    code: string;
    language: string;
  };
  prism: {
    theme: string;
    ['line-numbers']: boolean;
    ['show-invisibles']: boolean;
  };
};

const CodePopover: React.FC<{
  filename: string;
  render: Render;
  onPopoverClose: () => void;
}> = ({ onPopoverClose, render, filename }) => {
  return (
    <Popover
      onClose={onPopoverClose}
      className={styles.popover}
      data-testid="search-result-popover"
    >
      <Blob {...render} blob={{ ...render.blob, filename }} />
    </Popover>
  );
};

const SearchResult: React.FC<{
  filename: string;
  render?: Render;
  onSelectClick: () => void;
}> = ({ filename, render, onSelectClick }) => {
  const [isVisible, setVisible] = useState(false);
  const onViewClick = useCallback(() => {
    setVisible(isVisible => !isVisible);
  }, []);
  const onPopoverClose = useCallback(() => {
    setVisible(false);
  }, []);

  return (
    <Placeholder
      className={styles.placeholder}
      icon="editor-code"
      label={filename}
    >
      <Button isDefault disabled={render == null} onClick={onViewClick}>
        View
        {isVisible && (
          <CodePopover
            onPopoverClose={onPopoverClose}
            render={render!}
            filename={filename}
          />
        )}
      </Button>
      <Button isPrimary disabled={render == null} onClick={onSelectClick}>
        Select
      </Button>
    </Placeholder>
  );
};

const events = {
  onSelectClick: (e$: Observable<void, never>) =>
    e$.map(() => searchResultSelectClick()),
};

export default toJunction(events)(SearchResult);
