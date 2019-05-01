import React from 'react';
import { i18n } from '../../helpers';
import Accounts from './Accounts';
import Header from './Header';
import Highlighting from './Highlighting';
import Messages from './Messages';
import Jobs from './Jobs';
import Runs from './Runs';
import { Route, RunStatus, Message, JobStatus, Run } from '../../reducers';

type Theme = {
  name: string;
  slug: string;
};

type Props = {
  loading: boolean;
  route: Route;
  theme: {
    options: Theme[];
    selected: string;
  };
  'line-numbers': boolean;
  'show-invisibles': boolean;
  token: string;
  jobs: {
    name: string;
    slug: string;
    description: string;
    status: JobStatus;
    runs: Run[];
  }[];
  selectedJobName: string;
  selectedJobStatus: JobStatus;
  selectedJobRuns: Run[];
  selectedRunStatus: RunStatus;
  selectedRunMessages: Message[];
};

// @TODO(James) add demo code
const demo = {
  code: "console.log('hello')",
  filename: '',
  language: 'javascript'
};

const Page: React.FC<Props> = ({
  route,
  theme,
  ['line-numbers']: lineNumbers,
  ['show-invisibles']: showInvisibles,
  token,
  jobs,
  selectedJobName,
  selectedJobStatus,
  selectedJobRuns,
  selectedRunStatus,
  selectedRunMessages
}) => {
  switch (route.name) {
    case 'highlighting':
      return (
        <Highlighting
          demo={demo}
          theme={theme}
          line-numbers={lineNumbers}
          show-invisibles={showInvisibles}
        />
      );
    case 'accounts':
      return <Accounts token={token} />;
    case 'jobs':
      switch (true) {
        case Boolean(route.parts.run):
          return (
            <Messages
              {...{
                job: selectedJobName,
                job_id: route.parts.job,
                status: selectedRunStatus,
                messages: selectedRunMessages
              }}
            />
          );
        case Boolean(route.parts.job):
          return (
            <Runs
              name={selectedJobName}
              status={selectedJobStatus}
              runs={selectedJobRuns}
            />
          );
        default:
          return <Jobs jobs={jobs} />;
      }
    default:
      return <div>{i18n('route.404', route.name)}</div>;
  }
};

const SettingsPage: React.FC<Props> = props => (
  <div className="wrap">
    <Header route={props.route.name} loading={props.loading} />
    <Page {...props} />
  </div>
);

export default SettingsPage;
