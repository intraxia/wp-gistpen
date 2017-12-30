// @flow
import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChangeAction } from '../../actions';

export default sheetRouter({ default: '/editor' }, [
    ['/editor', R.always(routeChangeAction('editor'))],
    ['/commits', R.always(routeChangeAction('commits'))]
]);
