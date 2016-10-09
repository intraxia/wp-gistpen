// @flow
export type PrismState = {
    theme : string;
    'line-numbers' : boolean;
    'show-invisibles' : boolean;
};

export type SettingsState = {
    prism : PrismState;
};
