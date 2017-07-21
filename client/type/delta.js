// @flow
import type { Action } from './action';
export type SheetRouter = (route : string) => Action;

export type RouterDeltaOptions = {
    router : SheetRouter;
    param : string;
};
