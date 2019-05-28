import { State } from './state';
import { Route, Run, Job, JobStatus, RunStatus } from '../../reducers';
import { Nullable } from 'typescript-nullable';
import { SettingsPage } from '../../components';
import { jobIsSuccess } from '../../selectors';

const selectRoute = (state: { route: Nullable<Route> }) =>
  Nullable.withDefault({ name: 'highlighting', parts: {} }, state.route);

const selectThemes = (state: State) => ({
  options: Object.entries(state.globals.themes).map(([key, value]) => ({
    slug: key,
    name: value
  })),
  selected: state.prism.theme
});

const selectLineNumbers = (state: State) => state.prism['line-numbers'];

const selectShowInvisibles = (state: State) => state.prism['show-invisibles'];

const selectToken = (state: State) => state.gist.token;

const selectRunsForJob = (state: State, job: Job): Run[] =>
  Object.values(state.runs.items).filter(run => run.job === job.slug);

const selectJobs = (state: State) =>
  Object.values(state.jobs)
    .filter(jobIsSuccess)
    .map(({ response: job }) => ({
      ...job,
      runs: selectRunsForJob(state, job)
    }));

const selectLoading = (state: State) => state.ajax.running;

const selectViewedJob = (state: State): Nullable<Job> => {
  const slug = Nullable.maybe(null, route => route.parts.job, state.route);

  if (slug == null) {
    return null;
  }

  const job = state.jobs[slug];

  if (job.result === 'error') {
    return null;
  }

  return job.response;
};

const selectJobName = (state: State) =>
  Nullable.maybe('', job => job.slug, selectViewedJob(state));

const selectJobStatus = (state: State): JobStatus =>
  Nullable.maybe('idle', job => job.status, selectViewedJob(state));

const selectJobRuns = (state: State) =>
  Nullable.maybe(
    [],
    job => selectRunsForJob(state, job),
    selectViewedJob(state)
  );

const selectViewedRun = (state: State): Nullable<Run> => {
  const slug = Nullable.maybe(null, route => route.parts.run, state.route);

  if (slug == null) {
    return null;
  }

  return state.runs.items[slug];
};

const selectRunStatus = (state: State): RunStatus =>
  Nullable.maybe('finished', run => run.status, selectViewedRun(state));

const selectRunMessages = (state: State) =>
  Nullable.maybe(
    [],
    route =>
      Object.values(state.messages.items).filter(
        message => message.run_id === route.parts.run
      ),
    state.route
  );

const mapStateToProps = (
  state: State
): React.ComponentProps<typeof SettingsPage> => ({
  loading: selectLoading(state),
  route: selectRoute(state),
  theme: selectThemes(state),
  'line-numbers': selectLineNumbers(state),
  'show-invisibles': selectShowInvisibles(state),
  demo: state.globals.demo,
  token: selectToken(state),
  jobs: selectJobs(state),
  selectedJobName: selectJobName(state),
  selectedJobStatus: selectJobStatus(state),
  selectedJobRuns: selectJobRuns(state),
  selectedRunStatus: selectRunStatus(state),
  selectedRunMessages: selectRunMessages(state)
});

export default mapStateToProps;
