/* eslint-env jest */
import 'expect-puppeteer';
import shelljs from 'shelljs';
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
  const load = page.goto('http://localhost:8889');

  const run = shelljs.exec(`npm run env cli rewrite structure /%POSTNAME%/`, {
    silent: true
  });

  if (run.code !== 0) {
    throw new Error(`Setting up permalinks failed with error: ${run.stderr}`);
  }

  await Promise.all([load, setBrowserViewport('large')]);
});
