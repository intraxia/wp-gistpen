import { createAction } from 'typesafe-actions';

export const tinymceButtonClick = createAction('TINYMCE_BUTTON_CLICK');

export const tinymcePopupInsertClick = createAction(
  'TINYMCE_POPUP_INSERT_CLICK'
);

export const tinymcePopupCloseClick = createAction('TINYMCE_POPUP_CLOSE_CLICK');
