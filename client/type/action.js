import { THEME_CHANGE, LINE_NUMBERS_CHANGE, SHOW_INVISIBLES_CHANGE } from '../action';

export type Action<T, P> = {
    type : T;
    payload? : P;
};

export type ValuePayload<T> = {
    value : T;
};

export type ThemeChangeAction = Action<THEME_CHANGE, ValuePayload<string>>;

export type LineNumbersChangeAction = Action<LINE_NUMBERS_CHANGE, ValuePayload<boolean>>;

export type ShowInvisiblesChangeAction =  Action<SHOW_INVISIBLES_CHANGE, ValuePayload<boolean>>;

export type HighlightingAction = ThemeChangeAction | LineNumbersChangeAction | ShowInvisiblesChangeAction;
