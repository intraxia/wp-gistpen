// @flow
import './index.scss';
import '../../polyfills';
import { init } from '../../actions';
import store from './store';

const { __GISTPEN_TINYMCE__ } = global;

store.dispatch(init(__GISTPEN_TINYMCE__));
