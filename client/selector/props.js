// @flow
import type { Observable } from 'kefir';
import type {
    EditorPageState, EditorPageProps, SettingsState, SettingsProps,
    TinyMCEState, SearchProps, Job, Run, Message, CommitProps, CommitState
} from '../type';
import R from 'ramda';

export const selectJob = (state : SettingsState) : Job | void => {
    let job;

    if (state.route.name === 'jobs' &&
        typeof state.route.parts.job === 'string'
    ) {
        job = state.jobs[state.route.parts.job];

        if (job) {
            job = {
                ...job,
                runs: state.runs.filter((run : Run) : boolean => run.job === job.slug)
            };
        }
    }

    return job;
};

export const selectRun = (state : SettingsState) : Run | void => {
    let run;

    if (state.route.name === 'jobs' &&
        typeof state.route.parts.job === 'string' &&
        typeof state.route.parts.run === 'string'
    ) {
        const runId = state.route.parts.run;
        run = state.runs.find((run : Run) : boolean => run.ID === runId);

        if (run) {
            run = {
                ...run,
                messages: state.messages.filter((message : Message) => message.run_id === runId)
            };
        }
    }

    return run;
};

export const selectSettingsProps = (state$ : Observable<SettingsState>) : Observable<SettingsProps> =>
    state$.map((state : SettingsState) : SettingsProps => ({
        ...state,
        job: selectJob(state),
        run: selectRun(state)
    }))
        .skipDuplicates(R.equals);

export const selectEditorProps = (state$ : Observable<EditorPageState>) : Observable<EditorPageProps> =>
    state$.map(({ authors, globals, repo, route, editor, commits } : EditorPageState) : EditorPageProps => ({
        globals,
        repo,
        route,
        editor,
        // $FlowFixMe
        commits: commits.instances.map((instance : CommitState) : CommitProps => ({
            ...instance,
            author: authors.items[String(instance.author)]
        })),
        selectedCommit: commits.instances.find(instance => instance.ID === commits.selected)
    }));

export function selectSearchProps(state$ : Observable<TinyMCEState>) : Observable<SearchProps> {
    return state$;
}
