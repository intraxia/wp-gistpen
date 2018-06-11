// @flow
/* eslint-env mocha */
import type { EditorInstanceProps } from '../../../../types';
import '../../../../polyfills';
import { expect, use } from 'chai';
import { Kefir } from 'brookjs';
import * as Desalinate from 'brookjs-desalinate';

import Instance from '../';
import template from '../index.hbs';

const { plugin, send, prop, value } = Desalinate.chaiPlugin({ Kefir });

use(plugin);

describe('EditorInstanceComponent', () => {
    it('should render with hard return', () => {
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
                'languages': {}
            }
        };

        const el = Desalinate.createElementFromTemplate(template, initial);
        const props$ = send(prop(), [value(initial)]);

        expect(Instance(el, props$)).to.emitEffectsInTime([], () => {
            expect(el.querySelector('code').textContent).to.equal('\n');
        });
    });

    afterEach(() => {
        Desalinate.cleanup();
    });
});
