// @flow
import type { Job, Route, Run, Message } from './domain';

export type Toggle = 'on' | 'off';

export type Language = {
    ID : number;
    display_name : string;
    slug : string;
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
    gist_url : string | null;
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

export type JobsState = {
    [key : string] : Job;
};

export type GlobalsState = {|
    languages : {[key : string] : string; };
    root : string;
    nonce : string;
    url : string;
    ace_widths : Array<number>;
    statuses : { [key : string] : string; };
    themes : { [key : string] : string; };
    repo? : Repo;
|};

export type Cursor = false | [number, number,];

export type EditorSnapshot = {
    code : string;
    cursor : Cursor;
};

export type EditorHistory = {
    undo : Array<EditorSnapshot>;
    redo : Array<EditorSnapshot>;
};

export type EditorInstance = {
    key : string;
    filename : string;
    code : string;
    language : string;
    cursor : Cursor;
    history : EditorHistory;
};

export type Commit = {
    committed_at : string;
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
    optionsOpen : boolean;
};

export type CommitsState = {
    instances : Array<Commit>;
};

export type SearchState = {
    term : string;
    selection? : number;
};

export type MessagesState = Array<Message>;

export type RunsState = Array<Run>;

export type HasGlobalsState = {
    globals : GlobalsState;
};

export type HasPrismState = {
    prism : PrismState;
};

export type HasRouteState = {
    route : Route;
};

export type HasRepo = {
    repo : Repo;
};

export type HasEditorState = {
    editor : EditorState;
};

export type HasSearchState = {
    search : SearchState;
};

export type SettingsState = {
    globals : GlobalsState;
    prism : PrismState;
    gist : GistState;
    route : Route;
    jobs : JobsState;
    runs : RunsState;
    messages : MessagesState;
};

export type EditorPageState = {
    globals : GlobalsState;
    repo : Repo;
    editor : EditorState;
    commits : CommitsState;
    route : Route;
};

export type TinyMCEState = HasGlobalsState & HasSearchState;
