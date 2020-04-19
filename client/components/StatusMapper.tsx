import React from 'react';

type Renderer = ReturnType<React.FC<any>>;

type Props<T extends string> = {
  status: T;
  elements: {
    [K in T]: () => React.ReactElement;
  };
};

export const StatusMapper = <T extends string>({
  status,
  elements,
}: Props<T>): React.ReactElement => {
  return elements[status]();
};
