import './index.scss';
import '../../polyfills';
import { init } from '../../actions';
import store, { State } from './store';

declare global {
  interface Window {
    __GISTPEN_TINYMCE__: State;
  }
}

const { __GISTPEN_TINYMCE__ } = window;

store.dispatch(init(__GISTPEN_TINYMCE__));
