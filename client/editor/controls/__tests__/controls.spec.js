/* eslint-env mocha */
import '../../../polyfills';
import R from 'ramda';
import { expect } from 'chai';
import { constant, pool, stream, zip } from 'kefir';
import simulant from 'simulant';

import { EDITOR_UPDATE_CLICK } from '../../../action';

import ControlsComponent from '../';
import template from '../index.hbs';

const createEl$ = R.curry((template, props, callback = R.identity) => stream(emitter => {
    let wrapper = document.createElement('div');
    wrapper.innerHTML = template(props);
    const el = wrapper.firstElementChild;
    wrapper = null;
    document.body.appendChild(el);

    emitter.value(el);

    setTimeout(() => callback(el), 0);

    return () => document.body.removeChild(el);
}));

const createProps$ = (callback = R.identity) => stream(emitter => {
    const props$ = pool();

    emitter.value(props$);

    setTimeout(() => callback(props$), 0);
});

const createInstance$ = (el$, props$$, component) => zip([el$, props$$])
    .flatMap(R.apply(component));

describe('Editor Controls Component', () => {
    it('should emit an update click action', () => createInstance$(
        createEl$(template, {}, el => {
            simulant.fire(el.querySelector('[data-brk-onclick="onUpdateClick"]'), 'click');
        }),
        createProps$(props$ => {
            props$.plug(constant({}));
        }),
        ControlsComponent
    )
        // @todo assertion counting with `takeWhile`?
        .take(1)
        .map(val => expect(val).to.eql({
            type: EDITOR_UPDATE_CLICK
        }))
    );
});
