// @flow
import type { EditorInstance, EditorState, SettingsState, TinyMCEState,
    HasApiConfig, HasRepo, HasEditorState, HasRouteState } from './state';

export type RevisionPropsInstance = {
    date : string;
};
export type HasRevisionsProps = {
    revisions : Array<RevisionPropsInstance>;
};

export type SettingsProps = SettingsState;
export type EditorPageProps = HasApiConfig & HasRepo & HasEditorState & HasRevisionsProps & HasRouteState;

export type EditorInstanceProps = {
    instance : EditorInstance;
    editor : EditorState;
};
export type SearchProps = TinyMCEState;
