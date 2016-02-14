import React from 'react';
import Header from './header';
import Status from './status';

const Container = React.createClass({
    render() {
        return (
            <div className="wpr-app">
                <Header {...this.props} >
                    <Status message={this.props.message || {}} />
                </Header>
                {this.props.children}
            </div>
        );
    }
});

export default Container;
