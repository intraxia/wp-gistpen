// @flow
import type { RouteParts } from '../../type';
import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChangeAction } from '../../action';

export default sheetRouter({ default: '/highlighting' }, [
    ['/highlighting', R.always(routeChangeAction('highlighting'))],
    ['/accounts', R.always(routeChangeAction('accounts'))],
    ['/jobs', R.always(routeChangeAction('jobs')), [
        ['/:job', (params : RouteParts) => routeChangeAction('jobs', params), [
            ['/:run', (params : RouteParts) => routeChangeAction('jobs', params)]
        ]]
    ]],
]);
