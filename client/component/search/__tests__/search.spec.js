// @flow
/* eslint-env mocha */
import '../../../polyfills';
import type { Observable } from 'kefir';
import type { Action } from '../../../type';
import R from 'ramda';
import { expect } from 'chai';
import simulant from 'simulant';
import $$observable from 'symbol-observable';
import { createEl$, createProps$ } from '../../../desalinate';

import SearchComponent from '../';
import template from '../index.hbs';


describe('SearchComponent', () => {
    it('should be a function', () => {
        expect(SearchComponent).to.be.a('function');
    });

    it('should return an Observable when mounting', () : Promise<any> =>
        createEl$(template).zip(createProps$())
            .map(R.apply(SearchComponent))
            .take(1)
            .map((instance$ : Observable<Object>) : any =>
                // Downcast to avoid Flow error
                expect((instance$ : Object)[$$observable]).to.be.a('function')
            )
            .toPromise()
    );

    it('should emit an input event with the element value', () : Promise<any> =>
        createEl$(template, {}, (el : Element) => {
            const input = el.querySelector('input');

            if (!input || !(input instanceof HTMLInputElement)) {
                throw new Error('Element not found');
            }

            input.value = 'test';
            simulant.fire(input, 'input');
        })
            .zip(createProps$())
            .flatMap(R.apply(SearchComponent))
            .take(1)
            .map((action : Action) : any =>
                expect(action).to.eql({
                    type: 'SEARCH_INPUT',
                    payload: {
                        value: 'test'
                    }
                })
            )
            .toPromise()
    );

    it('should emit one input event with the latest value', () : Promise<any> =>
        createEl$(template, {}, (el : Element) => {
            const input = el.querySelector('input');

            if (!input || !(input instanceof HTMLInputElement)) {
                throw new Error('Element not found');
            }

            input.value = 'test';
            simulant.fire(input, 'input');

            setTimeout(() => {
                input.value = 'test2';
                simulant.fire(input, 'input');
            }, 250);
        })
            .zip(createProps$())
            .flatMap(R.apply(SearchComponent))
            .take(1)
            .map((action : Action) : any =>
                expect(action).to.eql({
                    type: 'SEARCH_INPUT',
                    payload: {
                        value: 'test2'
                    }
                })
            )
            .toPromise()
    );
});
