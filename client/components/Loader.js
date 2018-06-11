// @flow
// @jsx h
import './Loader.scss';
import { h } from 'brookjs-silt';

type Props = {
    text: string
};

export default ({ text }: Props) => (
    <div className={'loader'}>{text}</div>
);
