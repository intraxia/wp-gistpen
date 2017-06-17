// @flow
import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChangeAction } from '../../action';

export default sheetRouter({ default: '/editor' }, [
    ['/editor', R.always(routeChangeAction('editor'))],
    ['/revisions', R.always(routeChangeAction('revisions'))]
]);
