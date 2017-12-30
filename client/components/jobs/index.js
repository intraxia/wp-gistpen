// @flow
import type { Observable } from 'kefir';
import type { Action, HasMetaKey } from '../../types';
import R from 'ramda';
import { children, component, render } from 'brookjs';
import template from './index.hbs';
import row from './row';

export default component({
    children: children({
        jobRow: {
            factory: row,
            preplug: (instance$ : Observable<Action>, key : string) : Observable<Action & HasMetaKey> =>
                instance$.map(R.set(R.lensProp('meta'), { key }))
        }
    }),
    render: render(template)
});
