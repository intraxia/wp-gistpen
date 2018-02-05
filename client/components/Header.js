// @flow
// @jsx h
import { h, view } from 'brookjs-silt';
import classNames from 'classnames';
import { link } from '../helpers';

type HeaderProps = {
    route: string
};

export default ({ stream$ }: ObservableProps<HeaderProps>) => {
    const route$ = stream$.thru(view((props: HeaderProps) => props.route));

    return (
        <div data-brk-container="settingsHeader">
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
            </h2>
        </div>
    );
};
