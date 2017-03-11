//@flow

type CommandCallback = (editor : TinyMCEEditor) => void;

type TinyMCEButtonConfig = {

};

type WindowConfig = {

};

interface jQuery {
    append(content : string | HTMLElement) : jQuery;
    find(content : string) : jQuery;
}

interface Window {
    $el : jQuery;
    close() : void;
}

interface WindowManager {
    open(config : WindowConfig) : Window;
}

export interface TinyMCEEditor {
    addCommand(name : string, config : CommandCallback) : void;
    addButton(name : string, config : TinyMCEButtonConfig) : void;
    insertContent(content : string) : void;
    windowManager : WindowManager;
}

export type PluginFactory = (editor : TinyMCEEditor, url : string) => void;

export interface PluginManager  {
    add(name : string, callback : PluginFactory) : void;
}

export type TinyMCE = {
    PluginManager : PluginManager;
};
