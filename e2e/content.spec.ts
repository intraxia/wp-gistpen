/* eslint-env jest */
import languages from '../resources/languages.json';
import { createGistpen } from './helpers';

describe('content', () => {
  for (let [slug, displayName] of Object.entries(languages.list)) {
    if (process.env.SKIP_IMAGE_TESTS === 'true' && slug !== 'js') {
      continue;
    }

    const localPath = require('path').join(
      __dirname,
      '..',
      'resources',
      'samples',
      slug,
    );

    if (!require('fs').existsSync(localPath)) {
      it.todo(`should display ${displayName}`);
      continue;
    }

    it(`should display ${displayName} (${slug})`, async () => {
      let { repoUrl } = await createGistpen({
        description: `Test ${displayName}`,
        filename: `test.${slug}`,
        slug,
      });

      await page.goto(repoUrl);

      if ((languages.aliases as any)[slug]) {
        slug = (languages.aliases as any)[slug];
      }

      const element = await page.waitForSelector(`.gistpen.language-${slug}`);
      const screenshot = await element.screenshot();

      expect(screenshot).toMatchImageSnapshot();
    });
  }
});
