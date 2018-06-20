// @flow
import type { HasGlobalsState, HasRepo, HasEditorState, HasRouteState } from './state';
import type { Author, Job, Route, Commit } from './domain';
import type { Loopable } from './framework';

export type Theme = {
    name: string,
    key: string,
    selected: boolean
};

export type SettingsProps = {
    loading: boolean,
    route: Route,
    demo: {
        code: string,
        filename: string,
        language: string
    },
    themes: Loopable<string, Theme>,
    'line-numbers': boolean,
    'show-invisibles': boolean,
    token: string,
    jobs: Loopable<string, Job>
};

export type AjaxProps = {
    running: boolean
};

export type CommitProps = {
    ...Commit,
    author: ?Author
};

export type EditorPageProps = HasGlobalsState & HasRepo & HasEditorState & HasRouteState & {
    ajax: AjaxProps,
    commits: Array<CommitProps>,
    selectedCommit: ?CommitProps
};
