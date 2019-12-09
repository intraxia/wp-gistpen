import React from 'react';
import { Button } from '@wordpress/components';

const Back: React.FC<{ onClick: () => void }> = ({ onClick }) => {
  return <Button onClick={onClick}>Go Back</Button>;
};

export default Back;
