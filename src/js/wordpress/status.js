import React  from 'react';
import ReactCSSTransitionGroup from 'react-addons-css-transition-group';

export const STATUS_SUCCESS = 'STATUS_SUCCESS';
export const STATUS_NOTICE = 'STATUS_NOTICE';
export const STATUS_ERROR = 'STATUS_ERROR';

const Status = React.createClass({
    render: function() {
        let { status, text } = this.props.message;
        let className;

        switch(status) {
            case STATUS_SUCCESS:
                className = 'updated';
                break;
            case STATUS_ERROR:
                className = 'error';
                break;
            case STATUS_NOTICE:
                className = 'notice';
                break;
            default:
                className = 'wpr-status-placeholder';
                text = '';
                break;
        }

        return (
            <ReactCSSTransitionGroup
                    transitionName="wpr-fade"
                    transitionEnterTimeout={500}
                    transitionLeaveTimeout={500}>
                <div className={className} key={text}>
                    <p>{text}</p>
                </div>
            </ReactCSSTransitionGroup>
        );
    }
});

export default Status;
