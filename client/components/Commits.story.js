import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Commits from './Commits';

storiesOf('Commits', module)
    .add('default', () => (
        <Commits stream$={Kefir.constant({
            prism: {
                theme: 'default',
                'line-numbers': false,
                'show-invisibles': false
            },
            selectedCommit: {
                ID: 1,
                description: 'Commit Description',
                committed_at: '1970-01-01',
                author: {
                    name: 'Commit Author',
                    avatar: 'http://via.placeholder.com/48x48'
                },
                states: {
                    order: [1, 2],
                    dict: {
                        1: {
                            code: 'console.log("test");',
                            filename: 'test.js',
                            language: 'javascript'
                        },
                        2: {
                            code: 'console.log("test");',
                            filename: 'test.js',
                            language: 'javascript'
                        }
                    }
                }
            },
            commits: {
                order: [1, 2],
                dict: {
                    1: {
                        ID: 1,
                        description: 'Commit Description',
                        committed_at: '1970-01-01',
                        author: {
                            name: 'Commit Author',
                            avatar: 'http://via.placeholder.com/48x48'
                        }
                    },
                    2: {
                        ID: 2,
                        description: 'Commit Description',
                        committed_at: '1970-01-01',
                        author: {
                            name: 'Commit Author',
                            avatar: 'http://via.placeholder.com/48x48'
                        }
                    }
                }
            }
        })} />
    ));
