// @flow
import type { Emitter, Observable } from 'kefir';
import './index.scss';
import R from 'ramda';
import { component } from 'brookjs';
import { stream } from 'kefir';

export default component({
    onMount: R.curryN(2, () : Observable<void> => {
        return stream((emitter : Emitter<void, void>) => {
            console.log('Hello');
            emitter.end();
        });
    })
});
