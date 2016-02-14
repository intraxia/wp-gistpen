import { BehaviorSubject } from 'rx';
import assignDeep from 'object-assign-deep';

/**
 * Create a store subject with the provided default value.
 *
 * @param {Object} initial
 * @returns {BehaviorSubject}
 */
export function create(initial) {
    const store = new BehaviorSubject(initial);

    /**
     * Subscribe to a patch stream to update store subject.
     *
     * @param {Object} patch - Patch data
     */
    store.patch = (patch) => store.onNext(
        assignDeep({}, store.getValue(), patch)
    );

    return store;
}
