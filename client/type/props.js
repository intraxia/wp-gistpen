// @flow
import type { EditorInstance, EditorState, TinyMCEState,
    HasGlobalsState, HasRepo, HasEditorState, HasRouteState, GlobalsState,
    PrismState, GistState, JobsState } from './state';
import type { Author, Job, Route, Run } from './domain';

export type CommitProps = {
    author : ?Author;
    committed_at : string;
};
export type HasCommitsProps = {
    commits : Array<CommitProps>;
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
