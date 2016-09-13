import R from 'ramda';
import { stream } from 'kefir';
import hash from 'sheet-router/hash';
import router from '../settings/router'

const getAction = R.pipe(
    R.replace('#', '/'),
    router
);

export default function routerDelta() {
    return stream(emitter => {
        // Emit current route.
        emitter.value(getAction(window.location.hash));
        // Listen for hash changes.
        hash(R.pipe(getAction, emitter.value));
    });
}
