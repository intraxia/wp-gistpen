import { storiesOf } from '@storybook/react';
import React from 'react';
import SettingsPage from './';
import { JobStatus, RunStatus } from '../../reducers';

const highlightingRoute = {
  name: 'highlighting',
  parts: {}
};

const accountsRoute = {
  name: 'accounts',
  parts: {}
};

const jobsRoute = {
  name: 'jobs',
  parts: {}
};

const runRoute = {
  name: 'jobs',
  parts: {
    run: '1'
  }
};

const messagesRoute = {
  name: 'jobs',
  parts: {
    job: '1'
  }
};

const props = {
  loading: false,
  theme: {
    options: [
      {
        name: 'Default',
        slug: 'default'
      }
    ],
    selected: 'default'
  },
  'line-numbers': true,
  'show-invisibles': false,
  token: '12345',
  jobs: [],
  selectedJobName: 'Export',
  selectedJobStatus: 'idle' as JobStatus,
  selectedJobRuns: [],
  selectedRunStatus: 'finished' as RunStatus,
  selectedRunMessages: [],
  demo: {
    code: "console.log('hello')",
    filename: '',
    language: 'javascript'
  }
};

storiesOf('SettingsPage', module)
  .add('highlighting view', () => (
    <SettingsPage {...{ ...props, route: highlightingRoute }} />
  ))
  .add('accounts view', () => (
    <SettingsPage {...{ ...props, route: accountsRoute }} />
  ))
  .add('jobs view - no job selected', () => (
    <SettingsPage {...{ ...props, route: jobsRoute }} />
  ))
  .add('jobs view - job selected', () => (
    <SettingsPage {...{ ...props, route: runRoute }} />
  ))
  .add('jobs view - job & run selected', () => (
    <SettingsPage {...{ ...props, route: messagesRoute }} />
  ));
