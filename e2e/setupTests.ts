/* eslint-env jest */
import 'expect-puppeteer';
import execa from 'execa';
import { configureToMatchImageSnapshot } from 'jest-image-snapshot';
import { setBrowserViewport } from '@wordpress/e2e-test-utils';

const toMatchImageSnapshot = configureToMatchImageSnapshot({
  // @TODO(mAAdhaTTah) high for CI – can we reduce?
  failureThreshold: 0.06,
  failureThresholdType: 'percent',
  customDiffConfig: {
    threshold: 0.15
  }
});

expect.extend({ toMatchImageSnapshot });

const { PUPPETEER_TIMEOUT } = process.env;

// The Jest timeout is increased because these tests are a bit slow
jest.setTimeout(Number(PUPPETEER_TIMEOUT) || 100000);

beforeAll(async () => {
  await Promise.all([
    page.goto('http://localhost:8889'),
    execa.command(`npm run env cli rewrite structure /%POSTNAME%/`),
    setBrowserViewport('large')
  ]);
});
