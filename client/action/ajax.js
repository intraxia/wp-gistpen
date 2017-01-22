// @flow
import type { AjaxFinishedAction, AjaxFailedAction, ApiResponse,
    RepoApiResponse, RepoSaveSucceededAction, UserApiResponse,
    UserSaveSucceededAction } from '../type';

export const AJAX_FINISHED = 'AJAX_FINISHED';

/**
 * Creates a new Ajax finished action.
 *
 * @param {Object} response - Akax response
 * @returns {Action} Ajax finished action.
 */
export function ajaxFinishedAction(response : ApiResponse) : AjaxFinishedAction {
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
export function ajaxFailedAction(error : Error) : AjaxFailedAction {
    return {
        type: AJAX_FAILED,
        payload: { error },
        error: true
    };
}

export const REPO_SAVE_SUCCEEDED = 'REPO_SAVE_SUCCEEDED';

export function repoSaveSucceededAction(response : RepoApiResponse) : RepoSaveSucceededAction {
    return {
        type: REPO_SAVE_SUCCEEDED,
        payload: { response }
    };
}

export const USER_SAVE_SUCCEEDED = 'USER_SAVE_SUCCEEDED';

export function userSaveSucceededAction(response : UserApiResponse) : UserSaveSucceededAction {
    return {
        type: USER_SAVE_SUCCEEDED,
        payload: { response }
    };
}
