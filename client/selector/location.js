import R from 'ramda';

export const parseQueryString : ((query : string) => { [key : string] : string; }) = R.pipe(
    R.tail,
    R.split('&'),
    R.map(R.split('=')),
    R.fromPairs
);

export const buildQueryString : ((obj : { [key : string] : string; }) => string) = R.compose(
    R.concat('?'),
    R.join('&'),
    R.map(R.join('=')),
    R.toPairs
);

export const getRoute = R.pipe(
    (param : string) => parseQueryString(window.location.search)[param],
    R.defaultTo(''),
    R.concat('/')
);

export const getSearch = (param : string, route : string) : string => {
    return buildQueryString({
        ...parseQueryString(window.location.search),
        [param]: route
    });
};
export const getUrl = (param, route) => window.location.pathname + getSearch(param, route);
