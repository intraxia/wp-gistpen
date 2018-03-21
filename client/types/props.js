// @flow
import type { EditorInstance, EditorState,
    HasGlobalsState, HasRepo, HasEditorState, HasRouteState } from './state';
import type { Author, Job, Route } from './domain';
import type { Loopable } from './framework';

export type CommitProps = {
    author: ?Author;
    committed_at: string
};
export type HasCommitsProps = {
    commits: Array<CommitProps>
};

export type Theme = {
    name: string,
    key: string,
    selected: boolean
};

export type SettingsProps = {
    route: Route;
    demo: {
        code: string;
        filename: string;
        language: string
    };
    themes: Loopable<string, Theme>;
    'line-numbers': boolean;
    'show-invisibles': boolean;
    token: string;
    jobs: Loopable<string, Job>
};

export type EditorPageProps = HasGlobalsState & HasRepo & HasEditorState & HasCommitsProps & HasRouteState;

export type EditorInstanceProps = {
    instance: EditorInstance;
    editor: EditorState
};
