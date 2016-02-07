import React from 'react';
import { AJAX } from './actions';

const Status = React.createClass({
    render: function() {
        let DOM;

        switch(this.props.ajax) {
            case AJAX.UPDATING:
                DOM = this.renderUpdatingDOM();
                break;
            case AJAX.SUCCESS:
                DOM = this.renderSuccessDOM();
                break;
            case AJAX.ERROR:
                DOM = this.renderErrorDOM();
                break;
            case AJAX.IDLE:
            default:
                DOM = this.renderIdleDOM();
                break;
        }

        return DOM;
    },

    renderUpdatingDOM: function() {
        return (
           <div className="notice">
               <p>
                   <span
                       className="left spinner is-active"
                       style={{float: 'none', marginTop: '-4px'}}/>
                   {this.props.updating}
               </p>
           </div>
        );
    },

    renderSuccessDOM: function() {
        return (
            <div className="updated">
                <p>{this.props.success}</p>
            </div>
        );
    },

    renderErrorDOM: function() {
        return (
            <div className="error">
                <p>{this.props.error}</p>
            </div>
        );
    },

    renderIdleDOM: function() {
        return (
            <div style={{height: '58px'}}></div>
        );
    }
});

export default Status;
