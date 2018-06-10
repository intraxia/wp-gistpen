import { storiesOf } from '@storybook/react';
import { action } from '@storybook/addon-actions';
import { Kefir } from 'brookjs';
import { h, Aggregator } from 'brookjs-silt';
import Commits from './Commits.component';

storiesOf('Commits', module)
    .add('default', () => (
        <Aggregator action$={action$ => action$.observe(({ type, ...rest }) => action(type)(rest))}>
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
                commits: [
                    {
                        ID: 1,
                        description: 'Commit Description',
                        committed_at: '1970-01-01',
                        author: {
                            name: 'Commit Author',
                            avatar: 'http://via.placeholder.com/48x48'
                        }
                    },
                    {
                        ID: 2,
                        description: 'Commit Description',
                        committed_at: '1970-01-01',
                        author: {
                            name: 'Commit Author',
                            avatar: 'http://via.placeholder.com/48x48'
                        }
                    }
                ]
            })} />
        </Aggregator>
    ));
