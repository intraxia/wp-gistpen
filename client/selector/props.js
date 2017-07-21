// @flow
import type { Observable } from 'kefir';
import type { EditorPageState, EditorPageProps, SettingsState, SettingsProps,
    TinyMCEState, SearchProps } from '../type';
import R from 'ramda';

export function selectSettingsProps(state$ : Observable<SettingsState>) : Observable<SettingsProps> {
    return state$.map((state : SettingsState) => state).skipDuplicates(R.equals);
}

export function selectEditorProps(state$ : Observable<EditorPageState>) : Observable<EditorPageProps> {
    return state$.map(({ globals, repo, route, editor, commits } : EditorPageState) : EditorPageProps => ({
        globals,
        repo,
        route,
        editor,
        commits: commits.instances
    }));
}

export function selectSearchProps(state$ : Observable<TinyMCEState>) : Observable<SearchProps> {
    return state$;
}
