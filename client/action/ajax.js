export const AJAX_FINISHED = 'AJAX_FINISHED';

/**
 * Creates a new Ajax finished action.
 *
 * @param {Object} response - Akax response
 * @returns {Action} Ajax finished action.
 */
export function ajaxFinishedAction(response) {
    return {
        type: AJAX_FINISHED,
        payload: { response }
    };
}

export const AJAX_FAILED = 'AJAX_FAILED';

/**
 * Creates a new Ajax failed action.
 *
 * @param {Error} error - Error object,
 * @returns {Action} Ajax failed action.
 */
export function ajaxFailedAction(error) {
    return {
        type: AJAX_FAILED,
        payload: { error },
        error: true
    };
};
