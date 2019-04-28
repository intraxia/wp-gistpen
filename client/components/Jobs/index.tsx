import React from 'react';
import { i18n } from '../../helpers';
import Row from './Row';
import { Job } from './types';

type Props = {
  jobs: Job[];
};

const Jobs: React.FC<Props> = ({ jobs }) => (
  <div className="table">
    <h3 className="title">{i18n('jobs.title')}</h3>
    <table className="widefat striped">
      <thead>
        <tr>
          <th>{i18n('jobs.name')}</th>
          <th>{i18n('jobs.description')}</th>
          <th>{i18n('jobs.status')}</th>
          <th>{i18n('jobs.runs')}</th>
          <th>{i18n('jobs.dispatch')}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <th>{i18n('jobs.name')}</th>
          <th>{i18n('jobs.description')}</th>
          <th>{i18n('jobs.status')}</th>
          <th>{i18n('jobs.runs')}</th>
          <th>{i18n('jobs.dispatch')}</th>
        </tr>
      </tfoot>
      <tbody>
        {jobs.map(job => (
          <Row key={job.slug} {...job} />
        ))}
      </tbody>
    </table>
  </div>
);

export default Jobs;
