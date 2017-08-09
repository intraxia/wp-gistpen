// @flow
/* eslint-env mocha */
import type { EditorInstanceProps } from '../../../../type';
import '../../../../polyfills';
import R from 'ramda';
import { expect } from 'chai';
import { Kefir } from 'brookjs';
import * as Desalinate from 'brookjs/es/desalinate';

import onMount from '../onMount';
import template from '../index.hbs';

describe('EditorInstanceComponent', () => {
    it('should render with hard return', (done : Function) => {
        const initial : EditorInstanceProps = {
            'instance': {
                'filename': '',
                'code': '',
                'language': 'plaintext',
                'cursor': false,
                'history': {
                    'undo': [],
                    'redo': []
                },
                'key': 'new0'
            },
            'editor': {
                'description': 'Test Gistpen',
                'status': 'draft',
                'password': '',
                'gist_id': '',
                'sync': 'off',
                'instances': [
                    {
                        'filename': '',
                        'code': '',
                        'language': 'plaintext',
                        'cursor': false,
                        'history': {
                            'undo': [],
                            'redo': []
                        },
                        'key': 'new0'
                    }
                ],
                'width': '4',
                'theme': 'tomorrow',
                'invisibles': 'on',
                'tabs': 'off',
                'widths': [],
                'themes': {},
                'statuses': {},
                'languages': {},
                'optionsOpen': true
            }
        };
        const next : EditorInstanceProps = R.clone(initial);

        const el = Desalinate.createElementFromTemplate(template, initial);

        onMount(el, Kefir.sequentially(20, [initial, next])).observe({
            end() {
                expect(el.querySelector('code').textContent).to.equal('\n');

                done();
            }
        });
    });
});
