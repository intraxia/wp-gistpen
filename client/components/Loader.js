// @flow
// @jsx h
import './Loader.scss';
import { h } from 'brookjs-silt';
import { i18n } from '../helpers';

export default () => (
    <div className={'loader'}>{i18n('search.loading')}</div>
);
