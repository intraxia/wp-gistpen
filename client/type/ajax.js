// @flow
import type { Toggle, Repo, Blob } from './state';

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
