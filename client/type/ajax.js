// @flow
import type { Toggle, Repo, Blob } from './state';
import Kefir from 'kefir';

export type AjaxOptions = {
    method : string;
    body? : string;
    credentials? : 'include';
    headers? : {
        [key : string] : string;
    };
};
export type AjaxFunction = (url : string, opts : AjaxOptions) => Kefir.Observable<string, TypeError>;

export type Indent = '1' | '2' | '4' | '8';

export type UserApiResponse = {
    editor : {
        indent_width : Indent;
        invisibles_enabled : Toggle;
        tabs_enabled : Toggle;
        theme : string;
    };
};

export type RepoApiResponse = Repo;

export type SiteApiResponse = {
    gist : {
        token : string;
    };
    prism : {
        'line-numbers' : boolean;
        'show-invisibles' : boolean;
        theme : string;
    };
};

export type SearchApiResponse = Array<Blob>;

export type ApiResponse = RepoApiResponse | UserApiResponse | SiteApiResponse;
