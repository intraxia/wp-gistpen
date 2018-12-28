// @flow
// @jsx h
import type { Run as RunEntity, Loopable, ObservableProps } from '../types';
import { h, view, loop } from 'brookjs-silt';
import { link, i18n } from '../helpers';

type RunsProps = {
    name: string,
    runs: Loopable<string, RunEntity>
};

const Run = ({ stream$ }: ObservableProps<RunEntity>) => (
    <tr>
        <td>{stream$.thru(view((props: RunEntity) => props.ID))}</td>
        <td>{stream$.thru(view(props => props.status))}</td>
        <td>{stream$.thru(view(props => props.scheduled_at))}</td>
        <td>{stream$.thru(view(props => props.started_at))}</td>
        <td>{stream$.thru(view(props => props.finished_at))}</td>
        <td>
            {stream$.thru(view(({ job, ID }) => (
                <a href={link('wpgp_route', `jobs/${job}/${ID}`)}>
                    {i18n('run.messages.view')}
                </a>
            )))}
        </td>
    </tr>
);

export default ({ stream$ }: ObservableProps<RunsProps>) => (
    <div className="table">
        <h3 className="title">Runs for {stream$.thru(view((props: RunsProps) => props.name))} Job</h3>
        <p><strong>Current Status: {stream$.thru(view(props => props.status))}</strong></p>
        <p><a href={link('wpgp_route', 'jobs')}>&larr; back</a></p>

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
                {stream$.thru(view(props => props.runs)).thru(loop((child$, id) => (
                    <Run stream$={child$} key={id} />
                )))}
            </tbody>
        </table>
    </div>
);
