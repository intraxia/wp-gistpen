import React from 'react';
import { Button, ButtonGroup } from '@wordpress/components';
import { toJunction } from 'brookjs';
import { Stream } from 'kefir';
import { createNewClick, chooseExistingClick } from '../actions';
import styles from './CreateOrChoose.module.scss';

type Props = {
  onCreateNewClick: () => void;
  onChooseExistingClick: () => void;
  header: string;
  createLabel: string;
  chooseLabel: string;
};

const CreateOrChoose: React.FC<Props> = ({
  onCreateNewClick,
  onChooseExistingClick,
  header,
  createLabel,
  chooseLabel,
}) => {
  return (
    <div className={styles.container} data-testid="create-or-choose">
      <h3 className={styles.header}>{header}</h3>
      <ButtonGroup className={styles.group}>
        <Button isPrimary isLarge onClick={onCreateNewClick}>
          {createLabel}
        </Button>
        <div className={styles.or}>Or</div>
        <Button isTertiary isLarge onClick={onChooseExistingClick}>
          {chooseLabel}
        </Button>
      </ButtonGroup>
    </div>
  );
};

const events = {
  onCreateNewClick: (e$: Stream<void, never>) => e$.map(() => createNewClick()),
  onChooseExistingClick: (e$: Stream<void, never>) =>
    e$.map(() => chooseExistingClick()),
};

export default toJunction(events)(CreateOrChoose);
