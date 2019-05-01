import React from 'react';
import { link } from '../../../helpers';
import { RunStatus, Message as MessageEntity } from '../../../reducers';

type Props = {
  job: string;
  job_id: string;
  status: RunStatus;
  messages: MessageEntity[];
};

const Message: React.FC<MessageEntity> = ({ ID, text, level, logged_at }) => (
  <tr>
    <td>{ID}</td>
    <td>{text}</td>
    <td>{level}</td>
    <td>{logged_at}</td>
  </tr>
);

const Messages: React.FC<Props> = ({ job, job_id, status, messages }) => (
  <div className="table">
    <h3 className="title">Runs for {job} Run</h3>
    <p>
      <strong>Current Status: {status}</strong>
    </p>
    <p>
      <a href={link('wpgp_route', `jobs/${job_id}`)}>&larr; back</a>
    </p>

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
        {messages.map(message => (
          <Message key={message.ID} {...message} />
        ))}
      </tbody>
    </table>
  </div>
);

export default Messages;
