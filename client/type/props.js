// @flow
import type { EditorInstance, EditorState, TinyMCEState,
    HasGlobalsState, HasRepo, HasEditorState, HasRouteState, GlobalsState,
    PrismState, GistState, JobsState } from './state';
import type { Job, Route, Run } from './domain';

export type Commit = {
    committed_at : string;
};
export type HasCommitsProps = {
    commits : Array<Commit>;
};

export type SettingsProps = {
    globals : GlobalsState;
    prism : PrismState;
    gist : GistState;
    route : Route;
    jobs : JobsState;
    job? : Job;
    run? : Run;
};
export type EditorPageProps = HasGlobalsState & HasRepo & HasEditorState & HasCommitsProps & HasRouteState;

export type EditorInstanceProps = {
    instance : EditorInstance;
    editor : EditorState;
};
export type SearchProps = TinyMCEState;
