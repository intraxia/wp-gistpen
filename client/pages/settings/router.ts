import sheetRouter from 'sheet-router';
import { routeChange } from '../../actions';
import { RouteParts } from '../../reducers';

export default sheetRouter({ default: '/highlighting' }, [
  ['/highlighting', () => routeChange('highlighting')],
  ['/accounts', () => routeChange('accounts')],
  [
    '/jobs',
    () => routeChange('jobs'),
    [
      [
        '/:job',
        (params: RouteParts) => routeChange('jobs', params),
        [['/:run', (params: RouteParts) => routeChange('jobs', params)]]
      ]
    ]
  ]
]);
