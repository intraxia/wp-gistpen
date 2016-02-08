import React from 'react';

const Header = React.createClass({
    getInitialState: function () {
        return {route: window.location.hash.substr(1)};
    },

    componentDidMount: function () {
        window.addEventListener('hashchange', () => this.setState({route: window.location.hash.substr(1)}));
    },

    render: function() {
        const { route } = this.state;
        const isActive = (slug, i) => `/${slug}` === route ||
        ( '' === route && 0 === i);

        return (
            <div className="wpr-header">
                <h1>{this.props.title}</h1>
                {this.props.children}
                <h2 className="nav-tab-wrapper">
                    {this.props.tabs.map((tab, i) => {
                        const className = `nav-tab ${isActive(tab.slug, i) ? 'nav-tab-active' : '' }`;
                        return (
                            <a
                                href={`#/${tab.slug}`}
                                className={className}
                                key={i}>
                                {tab.display}
                            </a>
                        );
                    })}
                </h2>
            </div>
        );
    }
});

export default Header;
