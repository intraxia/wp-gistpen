/* eslint-env jest */
import 'expect-puppeteer';

const { PUPPETEER_TIMEOUT } = process.env;

// The Jest timeout is increased because these tests are a bit slow
jest.setTimeout(Number(PUPPETEER_TIMEOUT) || 100000);

beforeAll(async () => {
  await page.goto('http://localhost:8889');
});
