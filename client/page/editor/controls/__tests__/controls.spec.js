/* eslint-env mocha */
import '../../../../polyfills';
import { expect } from 'chai';
import { constant } from 'kefir';
import simulant from 'simulant';
import { createInstance$, createEl$, createProps$ } from '../../../../desalinate';

import { EDITOR_UPDATE_CLICK } from '../../../../action';

import ControlsComponent from '../';
import template from '../index.hbs';

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
