// @flow
import type { RouteParts } from '../../types';
import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChange } from '../../actions';

export default sheetRouter({ default: '/highlighting' }, [
    ['/highlighting', R.always(routeChange('highlighting'))],
    ['/accounts', R.always(routeChange('accounts'))],
    ['/jobs', R.always(routeChange('jobs')), [
        ['/:job', (params: RouteParts) => routeChange('jobs', params), [
            ['/:run', (params: RouteParts) => routeChange('jobs', params)]
        ]]
    ]],
]);
