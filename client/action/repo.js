/**
 * Dispatched when the Repo description changes.
 *
 * @type {string}
 */
export const REPO_DESCRIPTION_CHANGE = 'REPO_DESCRIPTION_CHANGE';

/**
 * Creates a new Repo Description Change Action.
 *
 * @param {string} value - Repo description.
 * @returns {Action} Repo Description Change Action.
 */
export const repoDescriptionChangeAction = function repoDescriptionChangeAction(value) {
    return {
        type: REPO_DESCRIPTION_CHANGE,
        payload: { value }
    };
};

/**
 * Dispatched when the Repo status changes.
 *
 * @type {string}
 */
export const REPO_STATUS_CHANGE = 'REPO_STATUS_CHANGE';

/**
 * Creates a new Repo Status Change Action.
 *
 * @param {string} value - Repo status..
 * @returns {Action} Repo Status Change Action.
 */
export const repoStatusChangeAction = function repoStatusChangeAction(value) {
    return {
        type: REPO_STATUS_CHANGE,
        payload: { value }
    };
};

/**
 * Dispatched when the Repo sync status changes.
 *
 * @type {string}
 */
export const REPO_SYNC_TOGGLE = 'REPO_SYNC_TOGGLE';

/**
 * Creates a new Repo Sync Change Action.
 *
 * @param {string} value - Repo sync status.
 * @returns {Action} Repo Sync Change Action.
 */
export const repoSyncToggleAction = function repoSyncToggleAction(value) {
    return {
        type: REPO_SYNC_TOGGLE,
        payload: { value }
    };
};
