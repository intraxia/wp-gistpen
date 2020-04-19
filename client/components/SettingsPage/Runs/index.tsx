import React from 'react';
import { link, i18n } from '../../../helpers';
import { Run as RunEntity, JobStatus } from '../../../reducers';

type RunsProps = {
  name: string;
  status: JobStatus;
  runs: RunEntity[];
};

const Run: React.FC<RunEntity & { job: string }> = ({
  ID,
  status,
  scheduled_at,
  started_at,
  finished_at,
  job,
}) => (
  <tr>
    <td>{ID}</td>
    <td>{status}</td>
    <td>{scheduled_at}</td>
    <td>{started_at}</td>
    <td>{finished_at}</td>
    <td>
      <a href={link('wpgp_route', `jobs/${job}/${ID}`)}>
        {i18n('run.messages.view')}
      </a>
    </td>
  </tr>
);

const Runs: React.FC<RunsProps> = ({ name, status, runs }) => (
  <div className="table">
    <h3 className="title">Runs for {name} Job</h3>
    <p>
      <strong>Current Status: {status}</strong>
    </p>
    <p>
      <a href={link('wpgp_route', 'jobs')}>&larr; back</a>
    </p>

    <table className="widefat striped">
      <thead>
        <tr>
          <th>{i18n('run.id')}</th>
          <th>{i18n('run.status')}</th>
          <th>{i18n('run.scheduled')}</th>
          <th>{i18n('run.started')}</th>
          <th>{i18n('run.finished')}</th>
          <th>{i18n('run.messages')}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>{i18n('run.id')}</th>
          <th>{i18n('run.status')}</th>
          <th>{i18n('run.scheduled')}</th>
          <th>{i18n('run.started')}</th>
          <th>{i18n('run.finished')}</th>
          <th>{i18n('run.messages')}</th>
        </tr>
      </tfoot>
      <tbody>
        {runs.map(run => (
          <Run key={run.ID} job={name} {...run} />
        ))}
      </tbody>
    </table>
  </div>
);

export default Runs;
