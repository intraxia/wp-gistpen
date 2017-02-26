//@flow

type TinyMCEButtonConfig = {

};

type CommandCallback = (editor : TinyMCEEditor) => void;

export interface TinyMCEEditor {
    addCommand(name : string, config : CommandCallback) : void;
    addButton(name : string, config : TinyMCEButtonConfig) : void;
}

export type PluginFactory = (editor : TinyMCEEditor, url : string) => void;

export interface PluginManager  {
    add(name : string, callback : PluginFactory) : void;
}

export type TinyMCE = {
    PluginManager : PluginManager;
};
