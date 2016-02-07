import React from 'react';

const Checkbox = React.createClass({
    render: function() {
        return (
            <tr>
                <th>
                    <label
                        htmlFor="wpr-checkbox">
                        {this.props.label}
                    </label>
                </th>
                <td>
                    <input
                        type="checkbox"
                        name="wpr-checkbox"
                        checked={this.props.checked}
                        onChange={this.props.onChange} />
                </td>
            </tr>
        );
    }
});

export default Checkbox;
