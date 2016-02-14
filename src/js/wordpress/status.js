import React from 'react';

export const STATUS_SUCCESS = 'STATUS_SUCCESS';
export const STATUS_NOTICE = 'STATUS_NOTICE';
export const STATUS_ERROR = 'STATUS_ERROR';

const Status = React.createClass({
    render: function() {
        const { status, text } = this.props.message;
        let DOM;

        switch(status) {
            case STATUS_SUCCESS:
                DOM = this.renderSuccessDOM(text);
                break;
            case STATUS_ERROR:
                DOM = this.renderErrorDOM(text);
                break;
            case STATUS_NOTICE:
                DOM = this.renderNoticeDOM(text);
                break;
            default:
                DOM = this.renderIdleDOM(text);
                break;
        }

        return DOM;
    },

    renderSuccessDOM: function(text) {
        return (
            <div className="updated">
                <p>{text}</p>
            </div>
        );
    },

    renderErrorDOM: function(text) {
        return (
            <div className="error">
                <p>{text}</p>
            </div>
        );
    },

    renderNoticeDOM: function(text) {
        return (
            <div className="notice">
                <p>{text}</p>
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
