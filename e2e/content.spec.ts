/* eslint-env jest */
import languages from '../resources/languages.json';
import execa from 'execa';

describe('content', () => {
  let repoId: string, repoUrl: string, blobId: string;

  beforeAll(async () => {
    let run = await execa.command(
      `npm run env cli gistpen repo create -- --description='Test' --status='publish' --porcelain`
    );

    repoId =
      run.stdout
        .split('\n')
        .filter(Boolean)
        .pop() ?? '';

    if (!repoId) {
      throw new Error(`Failed to parse repo id from output: ${run.stdout}`);
    }

    run = await execa.command(
      `npm run env cli gistpen repo get ${repoId} -- --field='html_url'`
    );

    repoUrl =
      run.stdout
        .split('\n')
        .filter(Boolean)
        .pop() ?? '';

    if (!repoUrl) {
      throw new Error(`Failed to parse repo url from output: ${run.stdout}`);
    }

    const jsDockerPath = `/var/www/src/wp-content/plugins/wp-gistpen/resources/samples/js`;

    run = await execa.command(
      process.env.SKIP_IMAGE_TESTS !== 'true'
        ? `npm run env cli gistpen blob create -- --repo_id=${repoId} --filename='placeholder' --porcelain`
        : `npm run env cli gistpen blob create ${jsDockerPath}  -- --repo_id=${repoId} --filename='js' --language='js' --porcelain`
    );

    blobId =
      run.stdout
        .split('\n')
        .filter(Boolean)
        .pop() ?? '';

    if (!blobId) {
      throw new Error(`Failed to parse blob id from output: ${run.stdout}`);
    }
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
