// @flow
import type { TinyMCE } from './tinymce';

// eslint-disable-next-line camelcase
declare var __webpack_public_path__ : string;

declare var tinymce : TinyMCE;

declare type Disposer = () => any;

declare interface ActionObservable<V, E=*> extends Observable<V, E> {
    ofType : (...types : Array<string>) => Observable<V, E>;
}
