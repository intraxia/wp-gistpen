import React from 'react';

const Header = React.createClass({
    render: function() {
        return (
            <div className="wpr-header">
                <h1>{this.props.title}</h1>
                {this.props.children}
                <h2 className="nav-tab-wrapper">
                    {this.props.tabs.map((tab, i) => <a href="#" className="nav-tab" key={i}>{tab}</a>)}
                </h2>
            </div>
        );
    }
});

export default Header;
