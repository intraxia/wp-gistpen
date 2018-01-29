// @flow
import { combineActionReducers } from 'brookjs';

export type AjaxState = {
    running : boolean;
};

const defaults : AjaxState = {
    running: false
};

const cond = [

];

export default combineActionReducers(cond, defaults);
