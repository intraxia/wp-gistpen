import { GlobalsState } from './state';

export * from './context';
export * from './state';

declare global {
  interface Window {
    __GISTPEN_GLOBALS__: GlobalsState;
    __GISTPEN_I18N__?: { [key: string]: string };
    __webpack_public_path__?: string;
  }
}
