// @flow
import type { EditorInstance, EditorState, EditorPageState, SettingsState, TinyMCEState } from './state';

export type SettingsProps = SettingsState;
export type EditorPageProps = EditorPageState;
export type EditorInstanceProps = {
    instance : EditorInstance;
    editor : EditorState;
};
export type TinyMCEProps = TinyMCEState;
