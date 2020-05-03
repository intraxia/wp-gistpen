import React from 'react';
import { Notice, Spinner } from '@wordpress/components';
import styles from './notices.module.scss';

export const ErrorNotice: React.FC<{ testid?: string }> = ({
  testid,
  children,
}) => {
  return (
    <Notice
      status="error"
      isDismissible={false}
      className={styles.notice}
      data-testid={testid}
    >
      {children}
    </Notice>
  );
};

export const WarningNotice: React.FC<{ isLoading?: boolean }> = ({
  isLoading = false,
  children,
}) => {
  return (
    <Notice status="warning" isDismissible={false} className={styles.notice}>
      {isLoading && <Spinner />}
      {children}
    </Notice>
  );
};
