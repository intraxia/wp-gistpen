import { combineReducers, createStore } from 'redux';
import { applyDelta, searchDelta, tinymcePluginDelta, webpackDelta } from '../../deltas';
import { ajaxReducer, globalsReducer, searchReducer } from '../../reducers';

const reducer = combineReducers({
    ajax: ajaxReducer,
    globals: globalsReducer,
    search: searchReducer
});

export default createStore(
    reducer,
    applyDelta(
        searchDelta,
        tinymcePluginDelta,
        webpackDelta
    )
);
