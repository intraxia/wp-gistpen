import R from 'ramda';

/**
 * Reducer creator function.
 *
 * @type {Function}
 */
const reducer = R.reduce((func, [type, reducer]) => {
    return (state, action) => {
        if (action.type === type) {
            state = reducer(state, action);
        }

        return func(state, action);
    };
});

/**
 * Create a new Action Reducer using a.
 *
 * @param {[string, function][]} cond - Array of action type/function tuples of type [ActionType, Reducer].
 * @param {Object} defaults - Default state.
 * @returns {Reducer} New action reducer.
 */
export default R.converge(reducer, [
    R.flip(defaults => R.pipe(R.identity, R.defaultTo(defaults))),
    R.identity
]);
