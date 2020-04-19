import sheetRouter from 'sheet-router';
import { routeChange } from '../../actions';

export default sheetRouter({ default: '/editor' }, [
  ['/editor', () => routeChange('editor')],
  ['/commits', () => routeChange('commits')],
]);
