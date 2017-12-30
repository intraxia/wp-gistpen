// @flow
import type { GistTokenChangeAction } from '../types';

export const GIST_TOKEN_CHANGE = 'GIST_TOKEN_CHANGE';

/**
 * Create a new gist token change actions.
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
