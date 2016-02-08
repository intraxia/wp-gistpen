import React from 'react';
import { Text } from '../wordpress';

const Stub = React.createClass({
    shouldComponentUpdate: function(nextProps) {
        return this.props.site.gist !== nextProps.site.gist;
    },

    render: function() {
        return (
            <div className="table">
                <h3 className="title">Coming soon!</h3>
            </div>
        );
    }
});

export default Stub;
