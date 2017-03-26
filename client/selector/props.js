// @flow
import type { Observable } from 'kefir';
import type { EditorPageState, EditorPageProps, SettingsState, SettingsProps } from '../type';

export function selectSettingsProps(state$ : Observable<SettingsState>) : Observable<SettingsProps> {
    return state$.map((state : SettingsState) => state);
}

export function selectEditorProps(state$ : Observable<EditorPageState>) : Observable<EditorPageProps> {
    return state$;
}
