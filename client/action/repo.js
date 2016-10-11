/**
 * Dispatched when the Repo description changes.
 *
 * @type {string}
 */
export const REPO_DESCRIPTION_CHANGE = 'REPO_DESCRIPTION_CHANGE';

/**
 * Creates a new RepdoDescription Change Action.
 *
 * @param {string} value - Repo description.
 * @returns {Action} RepoDescription Change Action.
 */
export const repoDescriptionChangeAction = function repoDescriptionChangeAction(value) {
    return {
        type: REPO_DESCRIPTION_CHANGE,
        payload: { value }
    };
};
