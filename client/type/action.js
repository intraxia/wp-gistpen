// @flow
import type { ApiResponse, RepoApiResponse, UserApiResponse } from './ajax';
import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE,
    AJAX_FINISHED, AJAX_FAILED, REPO_SAVE_SUCCEEDED, USER_SAVE_SUCCEEDED } from '../action';

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

export type HighlightingAction = ThemeChangeAction | LineNumbersChangeAction | ShowInvisiblesChangeAction;

export type AjaxAction = AjaxFinishedAction | AjaxFailedAction | RepoSaveSucceededAction | UserSaveSucceededAction;

export type Action = HighlightingAction | AjaxAction;
