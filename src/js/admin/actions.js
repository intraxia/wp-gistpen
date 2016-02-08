export const UPDATE_PRISM_THEME = 'UPDATE_PRISM_THEME';
export const TOGGLE_LINE_NUMBERS = 'TOGGLE_LINE_NUMBERS';
export const TOGGLE_SHOW_INVISIBLES = 'TOGGLE_SHOW_INVISIBLES';
export const UPDATE_GIST_TOKEN = 'UPDATE_GIST_TOKEN';
export const RESET_SITE_STATE = 'RESET_SITE_STATE';


export function updatePrismTheme(theme) {
    return {
        type: UPDATE_PRISM_THEME,
        theme
    };
}

export function toggleLineNumbers(enabled) {
    return {
        type: TOGGLE_LINE_NUMBERS,
        enabled
    };
}

export function toggleShowInvisibles(enabled) {
    return {
        type: TOGGLE_SHOW_INVISIBLES,
        enabled
    };
}

export function updateGistToken(token) {
    return {
        type: UPDATE_GIST_TOKEN,
        token
    }
}

export function resetSiteState(site) {
    return {
        type: RESET_SITE_STATE,
        site
    }
}
