import './index.scss';
import React from 'react';
import classNames from 'classnames';
import { i18n, link } from '../../../helpers';
import Loader from '../../Loader';

type Props = {
  loading: boolean;
  route: string;
};

const Header: React.FC<Props> = ({ route, loading }) => (
  <div className="wpgp-settings-header">
    <h1>Gistpen Settings</h1>
    <h2 className="nav-tab-wrapper">
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'highlighting',
        })}
        href={link('wpgp_route', 'highlighting')}
        data-testid="link-highlighting"
      >
        Highlighting
      </a>
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'accounts',
        })}
        href={link('wpgp_route', 'accounts')}
        data-testid="link-accounts"
      >
        Accounts
      </a>
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'jobs',
        })}
        href={link('wpgp_route', 'jobs')}
        data-testid="link-jobs"
      >
        Jobs
      </a>
      {loading ? <Loader text={i18n('settings.loading')} /> : null}
    </h2>
  </div>
);

export default Header;
