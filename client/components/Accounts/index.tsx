import React from 'react';
import { toJunction } from 'brookjs-silt';
import { gistTokenChange } from '../../actions';
import { Observable } from 'kefir';

type AccountProps = {
  token: string;
};

type Props = AccountProps & {
  onInput: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

const Accounts: React.FC<Props> = ({ onInput, token }) => (
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
              onInput={onInput}
              value={token}
              data-testid="token-input"
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
  onInput: (evt$: Observable<React.ChangeEvent<HTMLInputElement>, Error>) =>
    evt$.map(e => gistTokenChange(e.target.value))
};
export default toJunction<Props, typeof events>(events)(Accounts);
