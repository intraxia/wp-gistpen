import Kefir, { Observable, Stream, Property } from 'kefir';
import { ofType } from 'brookjs';
import * as t from 'io-ts';
import { Nullable } from 'typescript-nullable';
import { ajax$ } from 'kefir-ajax';
import {
  messagesFetchStarted,
  messagesFetchSucceeded,
  messagesFetchFailed,
  routeChange,
  jobFetchStarted,
  jobFetchSucceeded,
  jobFetchFailed,
  runsFetchStarted,
  runsFetchSucceeded,
  runsFetchFailed,
  jobDispatchClick,
  jobDispatchStarted,
  jobDispatchSucceeded,
  jobDispatchFailed,
} from '../actions';
import { RouteState, RunsState, JobsState, JobSuccess } from '../reducers';
import { RootAction } from '../util';
import { jobIsSuccess } from '../selectors';
import { GlobalsState } from '../globals';

type JobsServices = {
  ajax$: typeof ajax$;
};

type JobsDeltaState = {
  route: RouteState;
  runs: RunsState;
  globals: GlobalsState;
  jobs: JobsState;
};

const consoleResponse = t.type({
  status: t.string,
  messages: t.array(
    t.type({
      ID: t.string,
      run_id: t.string,
      text: t.string,
      level: t.union([
        t.literal('error'),
        t.literal('warning'),
        t.literal('success'),
        t.literal('info'),
        t.literal('debug'),
      ]),
      logged_at: t.string,
    }),
  ),
});

const jobStatus = t.union([t.literal('idle'), t.literal('processing')]);

const jobResponse = t.type({
  name: t.string,
  slug: t.string,
  description: t.string,
  rest_url: t.string,
  runs_url: t.string,
  status: jobStatus,
});

const runStatus = t.union([
  t.literal('scheduled'),
  t.literal('running'),
  t.literal('paused'),
  t.literal('finished'),
  t.literal('error'),
]);

const runEntity = t.type({
  ID: t.string,
  job: t.string,
  status: runStatus,
  scheduled_at: t.string,
  started_at: t.union([t.string, t.null]),
  finished_at: t.union([t.string, t.null]),
  rest_url: t.string,
  job_url: t.string,
  console_url: t.string,
});

const runResponse = t.array(runEntity);

const dispatchResponse = runEntity;

export const jobsDelta = ({ ajax$ }: JobsServices) => (
  actions$: Stream<RootAction, never>,
  state$: Property<JobsDeltaState, never>,
): Observable<RootAction, never> => {
  const fetch$ = state$
    .sampledBy(
      actions$
        .thru(ofType(routeChange))
        .filter(action => action.payload.name === 'jobs'),
    )
    .flatMapLatest(({ route, runs, globals, jobs }) => {
      if (Nullable.isNone(route)) {
        return Kefir.never();
      }

      if (typeof route.parts.run === 'string') {
        const run = runs.items[route.parts.run];

        if (Nullable.isNone(run)) {
          return Kefir.never();
        }

        return Kefir.concat<RootAction, never>([
          Kefir.constant(messagesFetchStarted()),
          ajax$(run.console_url, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'X-WP-Nonce': globals.nonce,
              'Content-Type': 'application/json',
            },
          })
            .flatMap(response => response.json())
            .flatMap(response =>
              consoleResponse
                .validate(response, [])
                .fold<Observable<RootAction, Error>>(
                  () =>
                    Kefir.constantError(new Error('API response was invalid')),
                  r => Kefir.constant(messagesFetchSucceeded(r)),
                ),
            )
            .flatMapErrors(err => Kefir.constant(messagesFetchFailed(err))),
        ]);
      }

      if (typeof route.parts.job === 'string') {
        const job = jobs[route.parts.job];

        if (Nullable.isNone(job)) {
          return Kefir.never();
        }

        if (!jobIsSuccess(job)) {
          return Kefir.never();
        }

        const job$ = Kefir.concat<RootAction, never>([
          Kefir.constant(jobFetchStarted()),
          ajax$(job.response.rest_url, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'X-WP-Nonce': globals.nonce,
              'Content-Type': 'application/json',
            },
          })
            .flatMap(response => response.json())
            .flatMap(response =>
              jobResponse
                .validate(response, [])
                .fold<Observable<RootAction, Error>>(
                  () =>
                    Kefir.constantError(new Error('API response was invalid')),
                  response => Kefir.constant(jobFetchSucceeded(response)),
                ),
            )
            .flatMapErrors(err =>
              Kefir.constant(jobFetchFailed(job.response.slug, err)),
            ),
        ]);

        const runs$ = Kefir.concat<RootAction, never>([
          Kefir.constant(runsFetchStarted()),
          ajax$(job.response.runs_url, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'X-WP-Nonce': globals.nonce,
              'Content-Type': 'application/json',
            },
          })
            .flatMap(response => response.json())
            .flatMap(response =>
              runResponse
                .validate(response, [])
                .fold<Observable<RootAction, Error>>(
                  () =>
                    Kefir.constantError(new Error('API response was invalid')),
                  response => Kefir.constant(runsFetchSucceeded(response)),
                ),
            )
            .flatMapErrors(err => Kefir.constant(runsFetchFailed(err))),
        ]);

        return Kefir.merge([job$, runs$]);
      }

      const jobs$ = [];

      for (const key in jobs) {
        const job = jobs[key];

        if (!jobIsSuccess(job)) {
          return Kefir.never();
        }

        const job$ = Kefir.concat<RootAction, never>([
          Kefir.constant(jobFetchStarted()),
          ajax$(job.response.rest_url, {
            method: 'GET',
            credentials: 'include',
            headers: {
              'X-WP-Nonce': globals.nonce,
              'Content-Type': 'application/json',
            },
          })
            .flatMap(response => response.json())
            .flatMap(response =>
              jobResponse
                .validate(response, [])
                .fold<Observable<RootAction, Error>>(
                  () =>
                    Kefir.constantError(new Error('API response was invalid')),
                  response => Kefir.constant(jobFetchSucceeded(response)),
                ),
            )
            .flatMapErrors(err =>
              Kefir.constant(jobFetchFailed(job.response.slug, err)),
            ),
        ]);

        jobs$.push(job$);
      }

      return Kefir.merge(jobs$);
    });

  const start$ = Kefir.combine(
    [actions$.thru(ofType(jobDispatchClick))],
    [state$],
    (action, state) => ({
      job: state.jobs[action.meta.key],
      globals: state.globals,
    }),
  )
    .filter(({ job }) => jobIsSuccess(job))
    .flatMap(({ globals, job }) =>
      Kefir.concat<RootAction, never>([
        Kefir.constant(jobDispatchStarted()),
        // We can assert this after the jobIsSuccess filter
        ajax$((job as JobSuccess).response.rest_url, {
          method: 'POST',
          credentials: 'include',
          headers: {
            'X-WP-Nonce': globals.nonce,
            'Content-Type': 'application/json',
          },
        })
          .flatMap(response => response.json())
          .flatMap(response =>
            dispatchResponse
              .validate(response, [])
              .fold<Observable<RootAction, Error>>(
                () =>
                  Kefir.constantError(new Error('API response was invalid')),
                response => Kefir.constant(jobDispatchSucceeded(response)),
              ),
          )
          .flatMapErrors(err => Kefir.constant(jobDispatchFailed(err))),
      ]),
    );

  return Kefir.merge([fetch$, start$]);
};
