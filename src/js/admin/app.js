import React from 'react';
import { observableDiff } from 'deep-diff';
import Highlighting from './highlighting';
import Accounts from './accounts';
import Stub from './stub';
import {
    gistTokenStream,
    themeStream,
    lineNumbersStream,
    showInvisiblesStream
} from './streams';
import { Container, Actions } from '../wordpress';

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

const App = React.createClass({
    getInitialState: function () {
        return {route: window.location.hash.substr(1)};
    },

    componentDidMount: function () {
        window.addEventListener(
            'hashchange',
            () => this.setState({
                route: window.location.hash.substr(1)
            })
        );
    },

    render: function () {
        let Child;

        switch (this.state.route) {
            case '/import':
            case '/export':
                Child = <Stub {...this.props} />;
                break;
            case '/accounts':
                Child = <Accounts
                    handleGistTokenChange={gistTokenStream}
                    {...this.props} />;
                break;
            case '/highlighting':
            default:
                Child = <Highlighting
                    handlePrismThemeChange={themeStream}
                    handleLineNumbersChange={lineNumbersStream}
                    handleShowInvisiblesChange={showInvisiblesStream}
                    {...this.props} />;
        }

        return (
            <Container
                message={this.props.message}
                title="Gistpen Settings"
                tabs={tabs}>
                {Child}
            </Container>
        );
    }
});

export default App;
