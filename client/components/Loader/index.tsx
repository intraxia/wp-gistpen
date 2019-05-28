import './Loader.scss';
import React from 'react';

type Props = {
  text: string;
};

const Loader: React.FC<Props> = ({ text }) => (
  <div className={'loader'}>{text}</div>
);

export default Loader;
