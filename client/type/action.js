// @flow
import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE } from '../action';

export type ThemeChangeAction = {
    type : $Keys<THEME_CHANGE>;
    payload : {
        value : string;
    };
};

export type LineNumbersChangeAction = {
    type : $Keys<LINE_NUMBERS_CHANGE>;
    payload : {
        value : boolean;
    };
};

export type ShowInvisiblesChangeAction = {
    type : $Keys<SHOW_INVISIBLES_CHANGE>;
    payload : {
        value : boolean;
    };
};

export type HighlightingAction = ThemeChangeAction | LineNumbersChangeAction | ShowInvisiblesChangeAction;

export type Action = HighlightingAction;
