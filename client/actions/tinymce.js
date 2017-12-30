// @flow
import type { TinyMCEButtonClickAction, TinyMCEPopupInsertClickAction, TinyMCEPopupCloseClickAction } from '../types';

export const TINYMCE_BUTTON_CLICK = 'TINYMCE_BUTTON_CLICK';

export function tinymceButtonClickAction() : TinyMCEButtonClickAction {
    return {
        type: TINYMCE_BUTTON_CLICK
    };
}

export const TINYMCE_POPUP_INSERT_CLICK = 'TINYMCE_POPUP_INSERT_CLICK';

export function tinymcePopupInsertClickAction() : TinyMCEPopupInsertClickAction {
    return {
        type: TINYMCE_POPUP_INSERT_CLICK
    };
}

export const TINYMCE_POPUP_CLOSE_CLICK = 'TINYMCE_POPUP_CLOSE_CLICK';

export function tinymcePopupCloseClickAction() : TinyMCEPopupCloseClickAction {
    return {
        type: TINYMCE_POPUP_CLOSE_CLICK
    };
}
