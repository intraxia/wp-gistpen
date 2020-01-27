import React from 'react';
import { toJunction } from 'brookjs';
import { gistTokenChange } from '../../../actions';
import { Observable } from 'kefir';

type AccountProps = {
  token: string;
};

type Props = AccountProps & {
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

const Accounts: React.FC<Props> = ({ onChange, token }) => (
  <div className="table">
    <h3 className="title">Sync Account Settings</h3>
    <table className="form-table">
      <tbody>
        <tr>
          <th>
            <label htmlFor="wpgp-token">Gist Token</label>
          </th>
          <td>
            <input
              type="text"
              name="wpgp-token"
              id="wpgp-token"
              className="regular-text"
              onChange={onChange}
              value={token}
              data-testid="gist-token"
            />
            <p className="description" id="wpgp-token-description">
              Create a new{' '}
              <a href="https://github.com/settings/tokens" target="_blank">
                Personal Access Token
              </a>{' '}
              with <code>gist</code> scope and paste it here.
            </p>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
);

const events = {
  onChange: (evt$: Observable<React.ChangeEvent<HTMLInputElement>, Error>) =>
    evt$.map(e => gistTokenChange(e.target.value))
};
export default toJunction(events)(Accounts);
