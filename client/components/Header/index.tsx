import React from 'react';
import classNames from 'classnames';
import jss from 'jss';
import nested from 'jss-nested';
import injectSheet from 'react-jss';
import { i18n, link } from '../../helpers';
import Loader from '../Loader';

type HeaderProps = {
  loading: boolean;
  route: string;
};

type HeaderClasses = {
  classes: {
    header: string;
  };
};

jss.use(nested());

const styles = {
  header: {
    '& .loader': {
      float: 'right'
    }
  }
};

type Props = HeaderProps & HeaderClasses;

const Header: React.FC<Props> = ({ route, loading, classes }) => (
  <div className={classes.header}>
    <h1>Gistpen Settings</h1>
    <h2 className="nav-tab-wrapper">
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'highlighting'
        })}
        href={link('wpgp_route', 'highlighting')}
      >
        Highlighting
      </a>
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'accounts'
        })}
        href={link('wpgp_route', 'accounts')}
      >
        Accounts
      </a>
      <a
        className={classNames({
          'nav-tab': true,
          'nav-tab-active': route === 'jobs'
        })}
        href={link('wpgp_route', 'jobs')}
      >
        Jobs
      </a>
      {loading ? <Loader text={i18n('settings.loading')} /> : null}
    </h2>
  </div>
);

export default injectSheet(styles)(Header);
