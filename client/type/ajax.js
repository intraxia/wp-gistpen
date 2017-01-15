// @flow
export type AjaxOptions = {
    method : string;
    body : string;
    credentials : ?string;
    headers : {
        [key : string] : string;
    };
};
