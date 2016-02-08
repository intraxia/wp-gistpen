import React from 'react';
import { Text } from '../wordpress';

const Accounts = React.createClass({
    shouldComponentUpdate: function(nextProps) {
        return this.props.site.gist !== nextProps.site.gist;
    },

    render: function() {
        return (
            <div className="table">
                <h3 className="title">Sync Account Settings</h3>
                <table className="form-table">
                    <tbody>
                        <Text
                            label="Gist Token"
                            value={this.props.site.gist.token}
                            onChange={this.props.handleGistTokenChange} />
                    </tbody>
                </table>
            </div>
        );
    }
});

export default Accounts;
