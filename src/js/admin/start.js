import React from 'react';
import { observableDiff } from 'deep-diff';
import Highlighting from './highlighting';
import Accounts from './accounts';
import Stub from './stub';
import { Container, Actions } from '../wordpress';
import {
    handlePrismThemeChange,
    handleLineNumbersChange,
    handleShowInvisiblesChange,
    handleGistTokenChange,
    handleServerUpdate
} from './dispatches';

const { AJAX } = Actions;

const tabs = [
    {
        slug: 'highlighting',
        display: 'Highlighting'
    },
    {
        slug: 'accounts',
        display: 'Accounts'
    },
    {
        slug: 'import',
        display: 'Import'
    },
    {
        slug: 'export',
        display: 'Export'
    }
];

const Start = React.createClass({
    getInitialState: function () {
        return {route: window.location.hash.substr(1)};
    },

    componentDidMount: function () {
        window.addEventListener('hashchange', () => this.setState({route: window.location.hash.substr(1)}));
    },

    render: function () {
        const props = Object.assign({}, this.props, {
            handlePrismThemeChange,
            handleLineNumbersChange,
            handleShowInvisiblesChange,
            handleGistTokenChange
        });

        let Child;

        switch (this.state.route) {
            case '/import':
            case '/export':
                Child = Stub;
                break;
            case '/accounts':
                Child = Accounts;
                break;
            case '/highlighting':
            default:
                Child = Highlighting;
        }

        return (
            <Container
                l10n={{
                    updating: "Updating Settings...",
                    success: "Successfully Updated Settings",
                    error: "Error Updating Settings"
                }}
                ajax={props.ajax}
                title="Gistpen Settings"
                tabs={tabs}>
                <Child {...props} />
            </Container>
        );
    },

    componentWillReceiveProps: function (nextProps) {
        // If we aren't idle or props haven't changed, bail.
        if (nextProps.ajax === AJAX.UPDATING || this.props === nextProps) {
            return;
        }

        let patch = {};

        observableDiff(this.props.site, nextProps.site, (delta) => {
            if ('E' !== delta.kind) {
                return;
            }

            const { path, rhs } = delta;

            switch (path.length) {
                case 1:
                    patch[path[0]] = rhs;
                    break;
                case 2:
                    patch[path[0]] = {};
                    patch[path[0]][path[1]] = rhs;
                    break;
                case 3:
                    patch[path[0]] = {};
                    patch[path[0]][path[1]] = {};
                    patch[path[0]][path[1]][path[2]] = rhs;
                    break;
            }
        });

        // Sanity check to ensure we got a delta.
        if ('{}' === JSON.stringify(patch)) {
            return;
        }

        handleServerUpdate(patch);
    }
});

export default Start;
