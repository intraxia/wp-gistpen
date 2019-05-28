import { Route } from '../reducers';

export const parseQueryString = ([, ...tail]: string) =>
  tail
    .join('')
    .split('&')
    .map(str => str.split('='))
    .reduce<{ [key: string]: string }>(
      (acc, [key, value]) => ({ ...acc, [key]: value }),
      {}
    );

export const buildQueryString = (obj: { [key: string]: string }) =>
  '?' +
  Object.entries(obj)
    .map(entry => entry.join('='))
    .join('&');

export const getRoute = (search: string, param: string): string =>
  '/' + (parseQueryString(search)[param] || '');

export const generateParam = (route: Route): string => {
  let param = route.name;

  if (route.name === 'jobs' && typeof route.parts.job === 'string') {
    param =
      typeof route.parts.run === 'string'
        ? `${param}/${route.parts.job}/${route.parts.run}`
        : `${param}/${route.parts.job}`;
  }

  return param;
};

export const getSearch = (param: string, route: Route): string => {
  return buildQueryString({
    ...parseQueryString(window.location.search),
    [param]: generateParam(route)
  });
};

export const getUrl = (param: string, route: Route) =>
  window.location.pathname + getSearch(param, route);
