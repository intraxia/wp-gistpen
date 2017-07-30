// @flow
export type RouteParts = {
    [key : string] : string | number;
};

export type Route = {
    name : string;
    parts? : RouteParts;
};
