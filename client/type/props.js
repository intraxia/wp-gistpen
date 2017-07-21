// @flow
import type { EditorInstance, EditorState, SettingsState, TinyMCEState,
    HasGlobalsState, HasRepo, HasEditorState, HasRouteState } from './state';

export type Commit = {
    committed_at : string;
};
export type HasCommitsProps = {
    commits : Array<Commit>;
};

export type SettingsProps = SettingsState;
export type EditorPageProps = HasGlobalsState & HasRepo & HasEditorState & HasCommitsProps & HasRouteState;

export type EditorInstanceProps = {
    instance : EditorInstance;
    editor : EditorState;
};
export type SearchProps = TinyMCEState;
