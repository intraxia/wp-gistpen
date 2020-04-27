import React, { useCallback, useState } from 'react';
import { Placeholder, Button, Popover } from '@wordpress/components';
import { toJunction, Maybe } from 'brookjs';
import { Observable } from 'kefir';
import { Blob } from '../components';
import { PrismState } from '../reducers';
import { searchResultSelectClick } from './actions';
import styles from './SearchResult.module.scss';

type Render = {
  blob: {
    filename: string;
    code: string;
    language: string;
  };
  prism: PrismState;
};

const CodePopover: React.FC<{
  render: Render;
  onPopoverClose: () => void;
}> = ({ onPopoverClose, render }) => {
  return (
    <Popover
      onClose={onPopoverClose}
      className={styles.popover}
      data-testid="search-result-popover"
    >
      <Blob {...render} />
    </Popover>
  );
};

const SearchResult: React.FC<{
  label: string;
  disabled?: boolean;
  render?: Maybe<Render>;
  onSelectClick: () => void;
}> = ({ label, disabled = false, render, onSelectClick }) => {
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
      label={label}
    >
      {render != null && (
        <Button isDefault onClick={onViewClick}>
          View
          {isVisible && (
            <CodePopover onPopoverClose={onPopoverClose} render={render} />
          )}
        </Button>
      )}
      <Button isPrimary disabled={disabled} onClick={onSelectClick}>
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
