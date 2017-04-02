// @flow
import type { GistTokenChangeAction } from '../type';

export const GIST_TOKEN_CHANGE = 'GIST_TOKEN_CHANGE';

/**
 * Create a new gist token change action.
 *
 * @param {string} value - Gist token value.
 * @returns {Action} Gist token change value.
 */
export function gistTokenChange(value : string) : GistTokenChangeAction {
    return {
        type: GIST_TOKEN_CHANGE,
        payload: { value }
    };
}
