/* eslint-env jest */
import 'expect-puppeteer';
import execa from 'execa';
import { configureToMatchImageSnapshot } from 'jest-image-snapshot';
import { setBrowserViewport, activatePlugin } from '@wordpress/e2e-test-utils';
import { resetSite } from './helpers';

const toMatchImageSnapshot = configureToMatchImageSnapshot({
  // @TODO(mAAdhaTTah) high for CI – can we reduce?
  failureThreshold: 0.06,
  failureThresholdType: 'percent',
  customDiffConfig: {
    threshold: 0.15,
  },
});

expect.extend({ toMatchImageSnapshot });

const { PUPPETEER_TIMEOUT } = process.env;

// The Jest timeout is increased because these tests are a bit slow
jest.setTimeout(Number(PUPPETEER_TIMEOUT) || 100000);

beforeAll(async () => {
  await Promise.all([
    (async () => {
      await resetSite();
      await execa.command(
        `npm run env run tests-cli rewrite structure /%POSTNAME%/`,
        { shell: '/bin/sh' },
      );
    })(),
    setBrowserViewport('large'),
  ]);
});
