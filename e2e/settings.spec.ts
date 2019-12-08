/* eslint-env jest */
import { visitAdminPage } from '@wordpress/e2e-test-utils';
import { resetSite } from './helpers';

// const LINK_HLING_SEL = '[data-testid="link-highlighting"]';
const LINK_ACCTS_SEL = '[data-testid="link-accounts"]';
// const LINK_JOBS_SEL = '[data-testid="link-jobs"]';

const THEME_SEL = '[data-testid="prism-theme"]';
const LN_SEL = '[data-testid="prism-line-numbers"]';
const SI_SEL = '[data-testid="prism-show-invisibles"]';
const DEMO_SEL = '[data-testid="prism-demo"]';

const TOKEN_SEL = '[data-testid="gist-token"]';

const defaultPrism = {
  theme: 'default',
  lineNumbers: false,
  showInvisibles: false
};

describe('settings', () => {
  beforeEach(async () => {
    await resetSite();
    await visitAdminPage('options-general.php', 'page=wp-gistpen');
  });

  afterAll(resetSite);

  it('should show the settings page with default settings', async () => {
    const values = await page.evaluate(
      (ts, lns, sis) => ({
        theme: (document.querySelector(ts) as HTMLSelectElement)?.value,
        lineNumbers: (document.querySelector(lns) as HTMLInputElement)?.checked,
        showInvisibles: (document.querySelector(sis) as HTMLInputElement)
          ?.checked
      }),
      THEME_SEL,
      LN_SEL,
      SI_SEL
    );

    expect(values).toEqual(defaultPrism);

    const demo = await page.$(DEMO_SEL);

    expect(await demo?.screenshot()).toMatchImageSnapshot();
  });

  it('should change & save the theme', async () => {
    await expect(page).toSelect(THEME_SEL, 'twilight');

    const response = await page.waitForResponse(
      res => res.url().includes('/site') && res.request().method() === 'PATCH'
    );

    expect(await response.json()).toEqual({
      prism: {
        theme: 'twilight',
        'line-numbers': false,
        'show-invisibles': false
      },
      gist: {
        token: ''
      }
    });

    const demo = await page.$(DEMO_SEL);

    expect(await demo?.screenshot()).toMatchImageSnapshot();
  });

  it('should change & save the line-numbers configuration', async () => {
    await expect(page).toClick(LN_SEL);

    const response = await page.waitForResponse(res =>
      res.url().includes('/site')
    );

    expect(await response.json()).toEqual({
      prism: {
        theme: 'default',
        'line-numbers': true,
        'show-invisibles': false
      },
      gist: {
        token: ''
      }
    });

    const demo = await page.$(DEMO_SEL);

    expect(await demo?.screenshot()).toMatchImageSnapshot();
  });

  it('should change & save the show-invisibles configuration', async () => {
    await expect(page).toClick(SI_SEL);

    const response = await page.waitForResponse(
      res => res.url().includes('/site') && res.request().method() === 'PATCH'
    );

    expect(await response.json()).toEqual({
      prism: {
        theme: 'default',
        'line-numbers': false,
        'show-invisibles': true
      },
      gist: {
        token: ''
      }
    });

    const demo = await page.$(DEMO_SEL);

    expect(await demo?.screenshot()).toMatchImageSnapshot();
  });

  it('should navigate to gist & save token', async () => {
    await expect(page).toClick(LINK_ACCTS_SEL);
    await expect(page).toFill(TOKEN_SEL, '123456abcd');

    const response = await page.waitForResponse(
      res => res.url().includes('/site') && res.request().method() === 'PATCH'
    );

    expect(await response.json()).toEqual({
      prism: {
        theme: 'default',
        'line-numbers': false,
        'show-invisibles': false
      },
      gist: {
        token: '123456abcd'
      }
    });
  });
});
