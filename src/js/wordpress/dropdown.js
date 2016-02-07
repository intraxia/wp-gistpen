import React from 'react';

const Dropdown = React.createClass({
    render: function() {
        return (
            <tr>
                <th><label htmlFor="wpr-dropdown">{this.props.label}</label></th>
                <td>
                    <select
                        name="wpr-dropdown"
                        value={this.props.selected}
                        onChange={this.props.onChange}>
                        {Object
                            .keys(this.props.options)
                            .map((slug, i) => <option
                                value={slug}
                                key={i}>
                                {this.props.options[slug]}
                            </option>)}
                    </select>
                </td>
            </tr>
        );
    }
});

export default Dropdown;
