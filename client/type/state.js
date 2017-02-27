// @flow
export type Toggle = 'on' | 'off';

export type Language = {
    ID : number;
    display_name : string;
    slug : string;
    // @deprecated
    prism_slug : string;
} | string;

export type Blob = {
    filename : string;
    code : string;
    language : Language;
    ID? : number;
    size? : number;
    raw_url? : string;
    edit_url? : string;
};

export type Repo = {
    ID? : number;
    description : string;
    status : string;
    password : string;
    gist_id : string;
    sync : Toggle;
    blobs : Array<Blob>;
    rest_url : string;
    commits_url : string;
    html_url : string;
    created_at : string;
    updated_at : string;
};

export type PrismState = {
    theme : string;
    'line-numbers' : boolean;
    'show-invisibles' : boolean;
};

export type GistState = {
    token : string;
};

export type RouteState  = 'highlighting' | 'accounts' | 'import' | 'export';

export type GlobalsState = {|
    languages : {[key : string] : string; };
    root : string;
    nonce : string;
    url : string;
    ace_themes : { [key : string] : string; };
    ace_widths : Array<number>;
    statuses : { [key : string] : string; };
    themes : { [key : string] : string; };
    repo? : Repo;
|};

export type Cursor = false | [number, number,];

export type EditorInstance = {
    key : string;
    filename : string;
    code : string;
    language : string;
    cursor : Cursor;
};

export type ApiConfig = {
    root : string;
    nonce : string;
    url : string;
};

export type EditorState = {
    description : string;
    status : string;
    password : string;
    password : string;
    gist_id : string;
    sync : Toggle;
    instances : Array<EditorInstance>;
    width : string;
    theme : string;
    invisibles : Toggle;
    tabs : Toggle;
};

export type HasGlobalsState = {
    globals : GlobalsState;
};

export type SettingsState = HasGlobalsState & {
    prism : PrismState;
    gist : GistState;
    route : RouteState;
};

export type EditorPageState = {
    api : ApiConfig;
    repo : Repo;
    editor : EditorState;
};
