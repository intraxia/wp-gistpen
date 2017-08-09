// @flow
import type { TinyMCE } from './tinymce';

// eslint-disable-next-line camelcase
declare var __webpack_public_path__ : string;

declare var tinymce : TinyMCE;

declare type Disposer = () => any;

declare interface ActionObservable<V, E=*> extends Observable<V, E> {
    ofType : (...types : Array<string>) => Observable<V, E>;
}

type jQueryObject = Array<Element>;

declare var jQuery : (html : string) => jQueryObject;

declare class ProxyEvent {
    delegateTarget : Element;
    target : Element;
    keyCode : string;
    altKey : boolean;
    ctrlKey : boolean;
    metaKey : boolean;
    shiftKey : boolean;
    preventDefault() : void;
}
