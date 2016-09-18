import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChangeAction } from '../action';

export default sheetRouter({ default: '/highlighting' }, [
    ['/highlighting', R.always(routeChangeAction('highlighting'))],
    ['/accounts', R.always(routeChangeAction('accounts'))],
    ['/import', R.always(routeChangeAction('import'))],
    ['/export', R.always(routeChangeAction('export'))]
]);
