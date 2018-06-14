// @flow
import type { RouteChangeAction } from './action';
export type SheetRouter = (route: string) => RouteChangeAction;

export type RouterDeltaOptions = {
    router: SheetRouter,
    param: string
};
