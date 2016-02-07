export const SET_AJAX_STATUS = 'SET_AJAX_STATUS';
export const AJAX = {
    UPDATING: 'updating',
    SUCCESS: 'success',
    ERROR: 'error',
    IDLE: 'idle'
};

export function setAjaxStatus(status) {
    return {
        type: SET_AJAX_STATUS,
        status
    };
}
