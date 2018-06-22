// @flow
import type { HasGlobalsState, HasRepo, Toggle, HasRouteState, EditorInstance } from './state';
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

export type EditorProps = {
    description: string,
    status: string,
    password: string,
    password: string,
    gist_id: string,
    sync: Toggle,
    instances: Loopable<string, EditorInstance>,
    width: string,
    theme: string,
    invisibles: Toggle,
    tabs: Toggle
};

export type EditorPageProps = HasGlobalsState & HasRepo & HasRouteState & {
    ajax: AjaxProps,
    commits: Loopable<number, CommitProps>,
    editor: EditorProps,
    selectedCommit: ?CommitProps
};
