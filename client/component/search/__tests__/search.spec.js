// @flow
/* eslint-env mocha */
import '../../../polyfills';
import type { Observable } from 'kefir';
import R from 'ramda';
import { expect } from 'chai';
import $$observable from 'symbol-observable';
import { createEl$, createProps$ } from '../../../desalinate';

import SearchComponent from '../';
import template from '../index.hbs';


describe('SearchComponent', () => {
    it('should be a function', () => {
        expect(SearchComponent).to.be.a('function');
    });

    it('should return an Observable when mounting', () : Observable<any> =>
        createEl$(template).zip(createProps$())
            .map(R.apply(SearchComponent))
            .take(1)
            .map((instance$ : Observable<Object>) : any =>
                // Downcast to avoid Flow error
                expect((instance$ : Object)[$$observable]).to.be.a('function')
            ));
});
