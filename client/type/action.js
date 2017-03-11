// @flow
import type { ApiResponse, RepoApiResponse, UserApiResponse } from './ajax';
import type { Blob } from './state';
import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE,
    AJAX_FINISHED, AJAX_FAILED, REPO_SAVE_SUCCEEDED, USER_SAVE_SUCCEEDED,
    TINYMCE_BUTTON_CLICK, TINYMCE_POPUP_INSERT_CLICK, TINYMCE_POPUP_CLOSE_CLICK,
    SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED, SEARCH_RESULT_SELECTION_CHANGE } from '../action';

export type ThemeChangeAction = {
    type : typeof THEME_CHANGE;
    payload : {
        value : string;
    };
};

export type LineNumbersChangeAction = {
    type : typeof LINE_NUMBERS_CHANGE;
    payload : {
        value : boolean;
    };
};

export type ShowInvisiblesChangeAction = {
    type : typeof SHOW_INVISIBLES_CHANGE;
    payload : {
        value : boolean;
    };
};

export type AjaxFinishedAction = {
    type : typeof AJAX_FINISHED;
    payload : {
        response : ApiResponse;
    };
};

export type AjaxFailedAction = {
    type : typeof AJAX_FAILED;
    payload : {
        error : Error;
    };
    error : true;
};

export type RepoSaveSucceededAction = {
    type : typeof REPO_SAVE_SUCCEEDED;
    payload : {
        response : RepoApiResponse;
    };
};

export type UserSaveSucceededAction = {
    type : typeof USER_SAVE_SUCCEEDED;
    payload : {
        response : UserApiResponse;
    };
};

export type TinyMCEButtonClickAction = {
    type : typeof TINYMCE_BUTTON_CLICK;
};

export type TinyMCEPopupInsertClickAction = {
    type : typeof TINYMCE_POPUP_INSERT_CLICK;
};

export type TinyMCEPopupCloseClickAction = {
    type : typeof TINYMCE_POPUP_CLOSE_CLICK;
};

export type SearchInputAction = {
    type : typeof SEARCH_INPUT;
    payload : {
        value : string;
    };
};

export type SearchResultsSucceededAction = {
    type : typeof SEARCH_RESULTS_SUCCEEDED;
    payload : {
        response : Array<Blob>;
    };
};

export type SearchResultSelectionChangeAction = {
    type : typeof SEARCH_RESULT_SELECTION_CHANGE,
    payload : {
        selection : string;
    };
};

export type HighlightingAction = ThemeChangeAction | LineNumbersChangeAction | ShowInvisiblesChangeAction;

export type AjaxAction = AjaxFinishedAction | AjaxFailedAction | RepoSaveSucceededAction | UserSaveSucceededAction;

export type TinyMCEAction = TinyMCEButtonClickAction | TinyMCEPopupInsertClickAction | TinyMCEPopupCloseClickAction;

export type SearchAction = SearchInputAction | SearchResultSelectionChangeAction;

export type Action = HighlightingAction | AjaxAction | TinyMCEAction | SearchAction;
