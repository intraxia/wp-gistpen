// @flow
// @jsx h
import type { ObservableProps } from '../types';
import { Collector, h, view } from 'brookjs-silt';
import { gistTokenChange } from '../actions';

type AccountProps = {
    token: string
};

export default ({ stream$ }: ObservableProps<AccountProps>) => (
    <Collector>
        <div className="table" data-brk-container="settingsAccounts">
            <h3 className="title">Sync Account Settings</h3>
            <table className="form-table">
                <tbody>
                    <tr>
                        <th>
                            <label htmlFor="wpgp-token">Gist Token</label>
                        </th>
                        <td>
                            <input type="text" name="wpgp-token"
                                id="wpgp-token" className="regular-text"
                                onChange={evt$ => evt$.map((e: SyntheticInputEvent<*>) => gistTokenChange(e.target.value))}
                                value={stream$.thru(view((prop: AccountProps) => prop.token))}/>
                            <p className="description" id="wpgp-token-description">
                            Create a new <a href="https://github.com/settings/tokens" target="_blank">Personal Access
                            Token</a>
                            with <code>gist</code> scope and paste it here.
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </Collector>
);
