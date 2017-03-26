// @flow
import type { EditorInstance, EditorState, EditorPageState, SettingsState } from './state';

export type SettingsProps = SettingsState;
export type EditorPageProps = EditorPageState;
export type EditorInstanceProps = {
    instance : EditorInstance;
    editor : EditorState;
};
