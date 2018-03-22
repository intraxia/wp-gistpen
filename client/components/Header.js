// @flow
// @jsx h
import { h, view } from 'brookjs-silt';
import classNames from 'classnames';
import jss from 'jss';
import nested from 'jss-nested';
import injectSheet from 'react-jss';
import { link } from '../helpers';
import Loader from './Loader';

type HeaderProps = {
    loading: boolean;
    route: string
};

type HeaderClasses = {
    classes: {
        header: string
    }
};

jss.use(nested());

const styles = {
    header: {
        '& .loader': {
            'float': 'right'
        }
    }
};

const Header = ({ stream$, classes }: ObservableProps<HeaderProps> & HeaderClasses) => {
    const route$ = stream$.thru(view((props: HeaderProps) => props.route));

    return (
        <div className={classes.header}>
            <h1>Gistpen Settings</h1>
            <h2 className="nav-tab-wrapper">
                <a className={route$.map(route => classNames({ 'nav-tab': true, 'nav-tab-active': route === 'highlighting' }))}
                    href={link('wpgp_route', 'highlighting')}>
                    Highlighting
                </a>
                <a className={route$.map(route => classNames({ 'nav-tab': true, 'nav-tab-active': route === 'accounts' }))}
                    href={link('wpgp_route', 'accounts')}>
                    Accounts
                </a>
                <a className={route$.map(route => classNames({ 'nav-tab': true, 'nav-tab-active': route === 'jobs' }))}
                    href={link('wpgp_route', 'jobs')}>
                    Jobs
                </a>
                {stream$.thru(view(props => props.loading))
                    .map(loading => loading ? <Loader /> : null)}
            </h2>
        </div>
    );
};

export default injectSheet(styles)(Header);
