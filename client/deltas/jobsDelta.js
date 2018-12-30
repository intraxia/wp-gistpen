// @flow
import type { Action, RouteChangeAction, JobDispatchClickAction,
    Route, Run, GlobalsState, Message, Job, RunStatus, HasMetaKey } from '../types';
import type { AjaxService, ObsResponse } from '../services';
import R from 'ramda';
import Kefir from 'kefir';
import { ofType } from 'brookjs';
import { messagesFetchStarted, messagesFetchSucceeded, messagesFetchFailed,
    routeChange, jobFetchStarted, jobFetchSucceeded, jobFetchFailed,
    runsFetchStarted, runsFetchSucceeded, runsFetchFailed, jobDispatchClick,
    jobDispatchStarted, jobDispatchSucceeded, jobDispatchFailed } from '../actions';

type JobsServices = {
    ajax$: AjaxService
};

type JobProps = {
    route: Route,
    runs: Array<Run>,
    globals: GlobalsState,
    jobs: {
        [key: string]: Job
    }
};

type GetConsoleResponse = {
    status: RunStatus,
    messages: Array<Message>
};
type GetJobResponse = Job;
type GetRunsResponse = Array<Run>;

export default R.curry((
    { ajax$ }: JobsServices,
    actions$: Kefir.Observable<Action>,
    state$: Kefir.Observable<JobProps>
): Kefir.Observable<Action> => {
    const fetch$ = state$.sampledBy(
        actions$.thru(ofType(routeChange))
            .filter((action: RouteChangeAction) => action.payload.name === 'jobs')
    )
        .flatMapLatest(({ route, runs, globals, jobs }: JobProps): Kefir.Observable<Action> => {
            if (typeof route.parts.run === 'string') {
                const run = runs.find((run: Run) => route.parts.run === run.ID);

                if (!run) {
                    return Kefir.never();
                }

                return Kefir.concat([
                    Kefir.constant(messagesFetchStarted()),
                    ajax$(run.console_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response: ObsResponse) => response.json())
                        .map((response: GetConsoleResponse) => messagesFetchSucceeded(response))
                        .flatMapErrors((err: TypeError) => Kefir.constant(messagesFetchFailed(err))),
                ]);
            }

            if (typeof route.parts.job === 'string') {
                const job = jobs[route.parts.job];

                if (!job) {
                    return Kefir.never();
                }

                const job$ = Kefir.concat([
                    Kefir.constant(jobFetchStarted()),
                    ajax$(job.rest_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response: ObsResponse) => response.json())
                        .map((response: GetJobResponse) => jobFetchSucceeded(response))
                        .flatMapErrors((err: TypeError) => Kefir.constant(jobFetchFailed(err))),
                ]);

                const runs$ = Kefir.concat([
                    Kefir.constant(runsFetchStarted()),
                    ajax$(job.runs_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response: ObsResponse) => response.json())
                        .map((response: GetRunsResponse) => runsFetchSucceeded(response))
                        .flatMapErrors((err: TypeError) => Kefir.constant(runsFetchFailed(err))),
                ]);

                return Kefir.merge([job$, runs$]);
            }

            const jobs$ = [];

            for (const key in jobs) {
                const job = jobs[key];

                const job$ = Kefir.concat([
                    Kefir.constant(jobFetchStarted()),
                    ajax$(job.rest_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response: ObsResponse) => response.json())
                        .map((response: GetJobResponse) => jobFetchSucceeded(response))
                        .flatMapErrors((err: TypeError) => Kefir.constant(jobFetchFailed(err))),
                ]);

                jobs$.push(job$);
            }

            return Kefir.merge(jobs$);
        });

    const start$ = Kefir.combine(
        [actions$.thru(ofType(jobDispatchClick))],
        [state$],
        (action: JobDispatchClickAction & HasMetaKey, state: JobProps) => ({
            job: state.jobs[action.meta.key],
            globals: state.globals
        })
    )
        .flatMap(({ globals, job }: { job: Job, globals: GlobalsState }) => Kefir.concat([
            Kefir.constant(jobDispatchStarted()),
            ajax$(job.rest_url, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-WP-Nonce': globals.nonce,
                    'Content-Type': 'application/json'
                }
            })
                .flatMap((response: ObsResponse) => response.json())
                .map(jobDispatchSucceeded)
                .flatMapErrors((err: TypeError) => Kefir.constant(jobDispatchFailed(err)))
        ]));

    return Kefir.merge([
        fetch$,
        start$
    ]);
}
);
