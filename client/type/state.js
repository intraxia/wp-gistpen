// @flow
export type PrismState = {
    theme : string;
    'line-numbers' : boolean;
    'show-invisibles' : boolean;
};

export type GistState = {
    token : string;
};

export type RouteState  = 'highlighting' | 'accounts' | 'import' | 'export';

export type ConstState = {
    languages : { [key : string] : string };
    root : string;
    nonce : string;
    url : string;
    ace_themes : { [key : string] : string };
    ace_widths : Array<number>;
    statuses : { [key : string] : string };
    themes : { [key : string] : string };
    repo? : Object;
};

export type SettingsState = {
    prism : PrismState;
    gist : GistState;
    route : RouteState;
    'const' : ConstState;
};
