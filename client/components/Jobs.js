// @flow
// @jsx h
import type { Observable } from 'kefir';
import { Collector, h, view, loop } from 'brookjs-silt';
import { i18n, link } from '../helpers';
import { jobDispatchClick } from '../actions';
import type { Job, ObservableProps } from '../types';

const Row = ({ stream$ }: ObservableProps<Job>) => (
    <tr>
        <td><strong>{stream$.thru(view((props: Job) => props.name))}</strong></td>
        <td>{stream$.thru(view(props => props.description))}</td>
        <td>
            {stream$.thru(view(props => props.status))
                .map(status => status || i18n('runs.loading'))}
        </td>
        <td>
            {stream$.thru(view(props => props.slug))
                .map(slug => <a href={link('wpgp_route', `jobs/${slug}`)}>{i18n('jobs.runs.view')}</a>)}
        </td>
        <td>
            {stream$.thru(view(props => props.ID)).map(ID => (
                <button className="button button-primary" onClick={(evt$: Observable<Event>) =>
                    evt$.map(jobDispatchClick)
                        // @todo this should not be added to the action here
                        .map(action => ({
                            ...action,
                            meta: { key: ID }
                        }))
                        .debounce(200)}>
                    {i18n('jobs.dispatch')}
                </button>
            ))}
        </td>
    </tr>
);

type JobsProps = {
    jobs: {
        order: Array<string>,
        dict: {
            [key: string]: Job
        }
    }
};

export default ({ stream$ }: ObservableProps<JobsProps>) => (
    <Collector>
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
    </Collector>
);
