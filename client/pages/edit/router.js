// @flow
import R from 'ramda';
import sheetRouter from 'sheet-router';
import { routeChange } from '../../actions';

export default sheetRouter({ default: '/editor' }, [
    ['/editor', R.always(routeChange('editor'))],
    ['/commits', R.always(routeChange('commits'))]
]);
