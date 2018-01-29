import { createStore } from 'redux';
import { applyDelta, searchDelta, tinymcePluginDelta, webpackDelta } from '../../deltas';
import { tinyMCEReducer } from '../../reducers';

export default createStore(
    tinyMCEReducer,
    applyDelta(
        searchDelta,
        tinymcePluginDelta,
        webpackDelta
    )
);
