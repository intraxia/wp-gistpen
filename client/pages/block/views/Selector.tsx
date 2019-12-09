import React, { useState, useCallback } from 'react';
import { ButtonGroup, Button } from '@wordpress/components';
import Add from './Add';
import Back from './Back';

const Search: React.FC<{ onBackClick: () => void }> = ({ onBackClick }) => {
  return (
    <div>
      Searching <Back onClick={onBackClick} />
    </div>
  );
};

const Selector: React.FC<{
  setIds: (repoId: number, blobId: number) => void;
}> = ({ setIds }) => {
  const [view, setView] = useState<'choose' | 'add' | 'search'>('choose');
  const onBackClick = useCallback(() => setView('choose'), [setView]);

  switch (view) {
    case 'choose':
      return (
        <div>
          <h3>No snippet has been selected</h3>
          <ButtonGroup>
            <Button
              data-testid="block-add-btn"
              isPrimary
              onClick={() => setView('add')}
            >
              Add New
            </Button>
            <Button
              data-testid="block-search-btn"
              isPrimary
              onClick={() => setView('search')}
            >
              Search
            </Button>
          </ButtonGroup>
        </div>
      );
    case 'add':
      return <Add onBackClick={onBackClick} setIds={setIds} />;
    case 'search':
      return <Search onBackClick={onBackClick} />;
  }
};

export default Selector;
