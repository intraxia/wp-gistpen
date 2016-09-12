import R from 'ramda';
import { combineReducers } from 'redux';

const defaultReducer = R.pipe(R.defaultTo({}), R.identity);

export default combineReducers({
    const: defaultReducer,
    route: defaultReducer,
    prism: defaultReducer,
    gist: defaultReducer
});
