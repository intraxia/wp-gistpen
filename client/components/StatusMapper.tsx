import React from 'react';

type Props<T extends string> = {
  status: T;
  elements: {
    [K in T]: () => React.ReactElement;
  };
};

export const StatusMapper = <T extends string>({
  status,
  elements,
}: Props<T>): React.ReactElement => elements[status]();
