import React from 'react';
import { Button } from '@wordpress/components';
import { toJunction } from 'brookjs';
import { Stream } from 'kefir';
import { createNewClick, chooseExistingClick } from '../../actions';
import styles from './CreateOrChoose.module.scss';

type Props = {
  onCreateNewClick: () => void;
  onChooseExistingClick: () => void;
};

const CreateOrChoose: React.FC<Props> = ({
  onCreateNewClick,
  onChooseExistingClick,
}) => {
  return (
    <div className={styles.container} data-testid="create-or-choose">
      <Button isPrimary isLarge onClick={onCreateNewClick}>
        Create new
      </Button>
      <div className={styles.or}>Or</div>
      <Button isTertiary isLarge onClick={onChooseExistingClick}>
        Choose from existing
      </Button>
    </div>
  );
};

const events = {
  onCreateNewClick: (e$: Stream<void, never>) => e$.map(() => createNewClick()),
  onChooseExistingClick: (e$: Stream<void, never>) =>
    e$.map(() => chooseExistingClick()),
};

export default toJunction(events)(CreateOrChoose);
