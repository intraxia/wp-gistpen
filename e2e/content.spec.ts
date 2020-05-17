/* eslint-env jest */
import languages from '../resources/languages.json';
import execa from 'execa';
import { createGistpen } from './helpers';

describe('content', () => {
  let repoUrl: string, blobId: string;

  beforeAll(async () => {
    ({ repoUrl, blobId } = await createGistpen());
  });

  for (let [slug, displayName] of Object.entries(languages.list)) {
    if (process.env.SKIP_IMAGE_TESTS === 'true' && slug !== 'js') {
      continue;
    }

    const localPath = require('path').join(
      __dirname,
      '..',
      'resources',
      'samples',
      slug
    );
    const dockerPath = `/var/www/src/wp-content/plugins/wp-gistpen/resources/samples/${slug}`;

    if (!require('fs').existsSync(localPath)) {
      it.todo(`should display ${displayName}`);
      continue;
    }

    it(`should display ${displayName} (${slug})`, async () => {
      if (!blobId) {
        throw new Error('Setup failed');
      }

      if (process.env.SKIP_IMAGE_TESTS !== 'true') {
        await execa.command(
          `npm run env cli gistpen blob update ${blobId} ${dockerPath} -- --filename='${slug}' --language='${slug}'`
        );
      }

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

