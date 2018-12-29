// @flow
// @jsx h
import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import { SettingsPage } from './SettingsPage';

storiesOf('SettingsPage', module)
    .add('highlighting view', () => (
        <SettingsPage stream$={Kefir.constant({
            loading: false,
            route: {
                name: 'highlighting',
                parts: {}
            },
            demo: {
                code: `console.log('test');`,
                filename: 'test.js',
                language: 'javascript'
            },
            themes: {
                order: ['twilight'],
                dict: {
                    twilight: {
                        name: 'Twilight',
                        key: 'twilight',
                        selected: true
                    }
                }
            },
            'line-numbers': true,
            'show-invisibles': true,
            token: 'ancsdf',
            jobs: {
                order: ['1', '2'],
                dict: {
                    '1': {
                        ID: '1',
                        name: 'Export',
                        slug: 'export',
                        description: 'Export things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    '2': {
                        ID: '2',
                        name: 'Import',
                        slug: 'import',
                        description: 'Import things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })}/>
    ))
    .add('accounts view', () => (
        <SettingsPage stream$={Kefir.constant({
            loading: false,
            route: {
                name: 'accounts',
                parts: {}
            },
            demo: {
                order: ['1'],
                dict: {
                    '1': {
                        code: `console.log('test');`,
                        filename: 'test.js',
                        language: 'javascript'
                    }
                }
            },
            themes: {
                order: ['twilight'],
                dict: {
                    twilight: {
                        name: 'Twilight',
                        key: 'twilight',
                        selected: true
                    }
                }
            },
            'line-numbers': true,
            'show-invisibles': true,
            token: 'ancsdf',
            jobs: {
                order: ['1', '2'],
                dict: {
                    '1': {
                        ID: '1',
                        name: 'Export',
                        slug: 'export',
                        description: 'Export things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    '2': {
                        ID: '2',
                        name: 'Import',
                        slug: 'import',
                        description: 'Import things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })} />
    ))
    .add('jobs view - no job selected', () => (
        <SettingsPage stream$={Kefir.constant({
            loading: false,
            route: {
                name: 'jobs',
                parts: {}
            },
            demo: {
                order: ['1'],
                dict: {
                    '1': {
                        code: `console.log('test');`,
                        filename: 'test.js',
                        language: 'javascript'
                    }
                }
            },
            themes: {
                order: ['twilight'],
                dict: {
                    twilight: {
                        name: 'Twilight',
                        key: 'twilight',
                        selected: true
                    }
                }
            },
            'line-numbers': true,
            'show-invisibles': true,
            token: 'ancsdf',
            jobs: {
                order: ['1', '2'],
                dict: {
                    '1': {
                        ID: '1',
                        name: 'Export',
                        slug: 'export',
                        description: 'Export things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    '2': {
                        ID: '2',
                        name: 'Import',
                        slug: 'import',
                        description: 'Import things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })} />
    ))
    .add('jobs view - job selected', () => (
        <SettingsPage stream$={Kefir.constant({
            loading: false,
            route: {
                name: 'jobs',
                parts: {
                    job: '1'
                }
            },
            demo: {
                order: ['1'],
                dict: {
                    '1': {
                        code: `console.log('test');`,
                        filename: 'test.js',
                        language: 'javascript'
                    }
                }
            },
            themes: {
                order: ['twilight'],
                dict: {
                    twilight: {
                        name: 'Twilight',
                        key: 'twilight',
                        selected: true
                    }
                }
            },
            'line-numbers': true,
            'show-invisibles': true,
            token: 'ancsdf',
            jobs: {
                order: ['1', '2'],
                dict: {
                    '1': {
                        ID: '1',
                        name: 'Export',
                        slug: 'export',
                        description: 'Export things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    '2': {
                        ID: '2',
                        name: 'Import',
                        slug: 'import',
                        description: 'Import things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })} />
    ))
    .add('jobs view - job & run selected', () => (
        <SettingsPage stream$={Kefir.constant({
            loading: false,
            route: {
                name: 'jobs',
                parts: {
                    job: '1',
                    run: '1'
                }
            },
            demo: {
                code: `console.log('test');`,
                filename: 'test.js',
                language: 'javascript'
            },
            themes: {
                order: ['twilight'],
                dict: {
                    twilight: {
                        name: 'Twilight',
                        key: 'twilight',
                        selected: true
                    }
                }
            },
            'line-numbers': true,
            'show-invisibles': true,
            token: 'ancsdf',
            jobs: {
                order: ['1', '2'],
                dict: {
                    '1': {
                        ID: '1',
                        name: 'Export',
                        slug: 'export',
                        description: 'Export things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    },
                    '2': {
                        ID: '2',
                        name: 'Import',
                        slug: 'import',
                        description: 'Import things',
                        status: 'idle',
                        rest_url: '',
                        runs_url: '',
                        runs: {
                            order: ['1'],
                            dict: {
                                '1': {
                                    ID: '1',
                                    job: 'export',
                                    status: 'finished',
                                    rest_url: '',
                                    job_url: '',
                                    console_url: '',
                                    scheduled_at: 'Yesterday',
                                    started_at: 'Today',
                                    finished_at: 'Tomorrow',
                                    messages: {
                                        order: ['1'],
                                        dict: {
                                            '1': {
                                                ID: '1',
                                                text: 'Success',
                                                level: 'info',
                                                run_id: '',
                                                logged_at: 'Now'
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })} />
    ));
