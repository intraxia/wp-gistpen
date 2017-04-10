// @flow
import type { ApiResponse, RepoApiResponse, UserApiResponse } from './ajax';
import type { Blob, Cursor, Toggle } from './state';
import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE,
    AJAX_FINISHED, AJAX_FAILED, REPO_SAVE_SUCCEEDED, USER_SAVE_SUCCEEDED,
    TINYMCE_BUTTON_CLICK, TINYMCE_POPUP_INSERT_CLICK, TINYMCE_POPUP_CLOSE_CLICK,
    SEARCH_INPUT, SEARCH_RESULTS_SUCCEEDED, SEARCH_RESULT_SELECTION_CHANGE,
    GIST_TOKEN_CHANGE, EDITOR_ADD_CLICK, EDITOR_OPTIONS_CLICK, EDITOR_THEME_CHANGE,
    EDITOR_TABS_TOGGLE, EDITOR_WIDTH_CHANGE, EDITOR_INVISIBLES_TOGGLE,
    EDITOR_UPDATE_CLICK, EDITOR_DELETE_CLICK, EDITOR_CURSOR_MOVE, EDITOR_MAKE_COMMENT,
    EDITOR_DESCRIPTION_CHANGE, EDITOR_STATUS_CHANGE, EDITOR_SYNC_TOGGLE,
    EDITOR_FILENAME_CHANGE, EDITOR_LANGUAGE_CHANGE, EDITOR_VALUE_CHANGE, EDITOR_INDENT,
    EDITOR_MAKE_NEWLINE, EDITOR_REDO, EDITOR_UNDO, ROUTE_CHANGE, EDITOR_REVISIONS_CLICK } from '../action';

export type HasMetaKey = {
    meta : {
        key : string;
    };
};

export type EditorValue = {
    code : string;
    cursor : Cursor;
};

export type EditorIndentValue = EditorValue & {
    inverse : boolean;
};

export type EditorAddClickAction = {
    type : typeof EDITOR_ADD_CLICK;
};


export type EditorCursorMoveAction = {
    type : typeof EDITOR_CURSOR_MOVE;
    payload : {
        cursor : Cursor;
    };
};

export type EditorDeleteClickAction = {
    type : typeof EDITOR_DELETE_CLICK;
};

export type EditorDescriptionChangeAction = {
    type : typeof EDITOR_DESCRIPTION_CHANGE;
    payload : {
        value : string;
    };
};

export type EditorFilenameChangeAction = {
    type : typeof EDITOR_FILENAME_CHANGE;
    payload : {
        value : string;
    };
};

export type EditorIndentAction = {
    type : typeof EDITOR_INDENT;
    payload : EditorIndentValue;
};

export type EditorInvisiblesToggleAction = {
    type : typeof EDITOR_INVISIBLES_TOGGLE;
    payload : {
        value : Toggle;
    };
};

export type EditorLanguageChangeAction = {
    type : typeof EDITOR_LANGUAGE_CHANGE;
    payload : {
        value : string;
    };
};

export type EditorMakeCommentAction = {
    type : typeof EDITOR_MAKE_COMMENT;
    payload : EditorValue;
};

export type EditorMakeNewLineAction = {
    type : typeof EDITOR_MAKE_NEWLINE;
    payload : EditorValue;
};

export type EditorOptionsClickAction = {
    type : typeof EDITOR_OPTIONS_CLICK;
};

export type EditorRedoAction = {
    type : typeof EDITOR_REDO;
};

export type EditorRevisionClickAction = {
    type : typeof EDITOR_REVISIONS_CLICK;
};

export type EditorStatusChangeAction = {
    type : typeof EDITOR_STATUS_CHANGE;
    payload : {
        value : string;
    };
};

export type EditorSyncChangeAction = {
    type : typeof EDITOR_SYNC_TOGGLE;
    payload : {
        value : Toggle;
    };
};

export type EditorTabsToggleAction = {
    type : typeof EDITOR_TABS_TOGGLE;
    payload : {
        value : string;
    };
};

export type EditorThemeChangeAction = {
    type : typeof EDITOR_THEME_CHANGE;
    payload : {
        value : string;
    };
};

export type EditorUndoAction = {
    type : typeof EDITOR_UNDO;
};

export type EditorUpdateClickAction = {
    type : typeof EDITOR_UPDATE_CLICK;
};

export type EditorValueChangeAction = {
    type : typeof EDITOR_VALUE_CHANGE;
    payload : EditorValue;
};

export type EditorWidthChangeAction = {
    type : typeof EDITOR_WIDTH_CHANGE;
    payload : {
        value : string;
    };
};

export type GistTokenChangeAction = {
    type : typeof GIST_TOKEN_CHANGE;
    payload : {
        value : string;
    };
};

export type RouteChangeAction = {
    type : typeof ROUTE_CHANGE;
    payload : {
        route : string;
    };
};

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

export type SettingsAction = GistTokenChangeAction | RouteChangeAction;

export type EditorAction = EditorAddClickAction | EditorCursorMoveAction
    | EditorFilenameChangeAction | EditorIndentAction | EditorInvisiblesToggleAction
    | EditorLanguageChangeAction | EditorMakeCommentAction | EditorMakeNewLineAction
    | EditorOptionsClickAction | EditorRedoAction | EditorStatusChangeAction
    | EditorSyncChangeAction | EditorTabsToggleAction | EditorThemeChangeAction
    | EditorUndoAction | EditorUpdateClickAction | EditorValueChangeAction
    | EditorWidthChangeAction | EditorDeleteClickAction | EditorDescriptionChangeAction;

export type HighlightingAction = ThemeChangeAction | LineNumbersChangeAction | ShowInvisiblesChangeAction;

export type AjaxAction = AjaxFinishedAction | AjaxFailedAction | RepoSaveSucceededAction | UserSaveSucceededAction;

export type TinyMCEAction = TinyMCEButtonClickAction | TinyMCEPopupInsertClickAction | TinyMCEPopupCloseClickAction;

export type SearchAction = SearchInputAction | SearchResultSelectionChangeAction;

export type Action = HighlightingAction | AjaxAction | TinyMCEAction | SearchAction;
