// @flow
// @jsx h
import { h, view, loop } from 'brookjs-silt';
import { i18n } from '../../helpers';
import type { Job, ObservableProps } from '../types';
import Row from './Row';

type JobsProps = {
    jobs: {
        order: Array<string>,
        dict: {
            [key: string]: Job
        }
    }
};

const Jobs = ({ stream$ }: ObservableProps<JobsProps>) => (
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
                {stream$.thru(view((props: JobsProps) => props.jobs))
                    .thru(loop((child$, id) => <Row stream$={child$} key={id} />))}
            </tbody>
        </table>
    </div>
);

export default Jobs;
