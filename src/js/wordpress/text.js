import React from 'react';

const Text = React.createClass({
    render: function() {
        return (
            <tr>
                <th><label htmlFor="wpr-dropdown">{this.props.label}</label></th>
                <td>
                    <input
                        type="text"
                        name="input-text"
                        className="regular-text"
                        placeholder={this.props.placeholder}
                        value={this.props.value}
                        onChange={this.props.onChange} />
                </td>
            </tr>
        );
    }
});

export default Text;
