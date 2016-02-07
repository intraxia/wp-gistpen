import React from 'react';
import Header from './header';
import Status from './status';

const Container = React.createClass({
    render() {
        return (
            <div className="wpr-app">
                <Header {...this.props} >
                    <Status
                        updating={this.props.l10n.updating}
                        success={this.props.l10n.success}
                        error={this.props.l10n.error}
                        ajax={this.props.ajax} />
                </Header>
                {this.props.children}
            </div>
        );
    }
});

export default Container;
