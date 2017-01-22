// @flow
import type { Toggle, Repo } from './state';

export type AjaxOptions = {
    method : string;
    body? : string;
    credentials? : string;
    headers? : {
        [key : string] : string;
    };
};

type Indent = '1' | '2' | '4' | '8';

export type UserApiResponse = {
    editor : {
        indent_width : Indent;
        invisibles_enabled : Toggle;
        tabs_enabled : Toggle;
        theme : string;
    };
};

export type RepoApiResponse = Repo;

export type ApiResponse = RepoApiResponse | UserApiResponse;
