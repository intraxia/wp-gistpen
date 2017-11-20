// @flow
import type { Action, RouteChangeAction, JobDispatchClickAction,
    Route, Run, GlobalsState, Message, Job, RunStatus, HasMetaKey } from '../type';
import type { AjaxService, ObsResponse } from '../service';
import R from 'ramda';
import { Kefir, ofType } from 'brookjs';
import { MESSAGES_FETCH_STARTED, MESSAGES_FETCH_SUCCEEDED, MESSAGES_FETCH_FAILED,
    ROUTE_CHANGE, JOB_FETCH_STARTED, JOB_FETCH_SUCCEEDED, JOB_FETCH_FAILED,
    RUNS_FETCH_STARTED, RUNS_FETCH_SUCCEEDED, RUNS_FETCH_FAILED, JOB_DISPATCH_CLICK,
    jobDispatchStarted, jobDispatchSucceeded, jobDispatchFailed } from '../action';

type JobsServices = {
    ajax$ : AjaxService;
};

type JobProps = {
    route : Route;
    runs : Array<Run>;
    globals : GlobalsState;
    jobs : {
        [key : string] : Job;
    };
};

type GetConsoleResponse = {
    status : RunStatus;
    messages : Array<Message>;
};
type GetJobResponse = Job;
type GetRunsResponse = Array<Run>;

export default R.curry((
    { ajax$ } : JobsServices,
    actions$ : Kefir.Observable<Action>,
    state$ : Kefir.Observable<JobProps>
) : Kefir.Observable<Action> => {
    const fetch$ = state$.sampledBy(
        actions$.thru(ofType(ROUTE_CHANGE))
            .filter((action : RouteChangeAction) => action.payload.name === 'jobs')
    )
        .flatMapLatest(({ route, runs, globals, jobs } : JobProps) : Kefir.Observable<Action> => {
            if (typeof route.parts.run === 'string') {
                const run = runs.find((run : Run) => route.parts.run === run.ID);

                if (!run) {
                    return Kefir.never();
                }

                return Kefir.concat([
                    Kefir.constant({
                        type: MESSAGES_FETCH_STARTED
                    }),
                    ajax$(run.console_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response : ObsResponse) => response.json())
                        .map((response : GetConsoleResponse) => ({
                            type: MESSAGES_FETCH_SUCCEEDED,
                            payload: { response }
                        }))
                        .flatMapErrors((err : TypeError) => Kefir.constant({
                            type: MESSAGES_FETCH_FAILED,
                            payload: err,
                            error: true
                        })),
                ]);
            }

            if (typeof route.parts.job === 'string') {
                const job = jobs[route.parts.job];

                if (!job) {
                    return Kefir.never();
                }

                const job$ = Kefir.concat([
                    Kefir.constant({
                        type: JOB_FETCH_STARTED
                    }),
                    ajax$(job.rest_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response : ObsResponse) => response.json())
                        .map((response : GetJobResponse) => ({
                            type: JOB_FETCH_SUCCEEDED,
                            payload: { response }
                        }))
                        .flatMapErrors((err : TypeError) => Kefir.constant({
                            type: JOB_FETCH_FAILED,
                            payload: err,
                            error: true
                        })),
                ]);

                const runs$ = Kefir.concat([
                    Kefir.constant({
                        type: RUNS_FETCH_STARTED
                    }),
                    ajax$(job.runs_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response : ObsResponse) => response.json())
                        .map((response : GetRunsResponse) => ({
                            type: RUNS_FETCH_SUCCEEDED,
                            payload: { response }
                        }))
                        .flatMapErrors((err : TypeError) => Kefir.constant({
                            type: RUNS_FETCH_FAILED,
                            payload: err,
                            error: true
                        })),
                ]);

                return Kefir.merge([job$, runs$]);
            }

            const jobs$ = [];

            for (const key in jobs) {
                const job = jobs[key];

                const job$ = Kefir.concat([
                    Kefir.constant({
                        type: JOB_FETCH_STARTED
                    }),
                    ajax$(job.rest_url, {
                        method: 'GET',
                        credentials: 'include',
                        headers: {
                            'X-WP-Nonce': globals.nonce,
                            'Content-Type': 'application/json'
                        }
                    })
                        .flatMap((response : ObsResponse) => response.json())
                        .map((response : GetJobResponse) => ({
                            type: JOB_FETCH_SUCCEEDED,
                            payload: { response }
                        }))
                        .flatMapErrors((err : TypeError) => Kefir.constant({
                            type: JOB_FETCH_FAILED,
                            payload: err,
                            error: true
                        })),
                ]);

                jobs$.push(job$);
            }

            return Kefir.merge(jobs$);
        });

    const start$ = Kefir.combine(
        [actions$.thru(ofType(JOB_DISPATCH_CLICK))],
        [state$],
        (action : JobDispatchClickAction & HasMetaKey, state : JobProps) => ({
            job: state.jobs[action.meta.key],
            globals: state.globals
        })
    )
        .flatMap(({ globals, job } : { job : Job; globals : GlobalsState; }) => Kefir.concat([
            Kefir.constant(jobDispatchStarted()),
            ajax$(job.rest_url, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-WP-Nonce': globals.nonce,
                    'Content-Type': 'application/json'
                }
            })
                .flatMap((response : ObsResponse) => response.json())
                .map(jobDispatchSucceeded)
                .flatMapErrors((err : TypeError) => Kefir.constant(jobDispatchFailed(err)))
        ]));

    return Kefir.merge([
        fetch$,
        start$
    ]);
}
);
