// @flow
// @jsx h
import type { Message as MessageEntity, RunStatus, ObservableProps } from '../types';
import { Collector, h, view, loop } from 'brookjs-silt';
import { link } from '../helpers';

type ID = string;

type MessagesProps = {
    job: string,
    job_id: string,
    status: RunStatus,
    messages: {
        order: Array<ID>,
        dict: {
            [key: ID]: MessageEntity
        }
    }
};

const Message = ({ stream$ }: ObservableProps<MessageEntity>) => (
    <tr>
        <td>{stream$.thru(view(props => props.ID))}</td>
        <td>{stream$.thru(view(props => props.text))}</td>
        <td>{stream$.thru(view(props => props.level))}</td>
        <td>{stream$.thru(view(props => props.logged_at))}</td>
    </tr>
);

export default ({ stream$ }: ObservableProps<MessagesProps>) => (
    <Collector>
        <div className="table">
            <h3 className="title">Runs for {stream$.thru(view((props: MessagesProps) => props.job))} Run</h3>
            <p><strong>Current Status: {stream$.thru(view(props => props.status))}</strong></p>
            <p><a href={stream$.thru(view(props => props.job_id)).map(id => link('wpgp_route', `jobs/${id}`))}>&larr; back</a></p>

            <table className="widefat striped">
                <thead>
                    <tr>
                        <th>Message ID</th>
                        <th>Message Text</th>
                        <th>Message Level</th>
                        <th>Message Log Time</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Message ID</th>
                        <th>Message Text</th>
                        <th>Message Level</th>
                        <th>Message Log Time</th>
                    </tr>
                </tfoot>
                <tbody>
                    {stream$.thru(view(props => props.messages))
                        .thru(loop((msg$, id) => (
                            <Message stream$={msg$} key={id} />
                        )))
                    }
                </tbody>
            </table>
        </div>
    </Collector>
);
