/* eslint-env jest */
import languages from '../resources/languages.json';
import shelljs from 'shelljs';

describe('content', () => {
  let repoId: string, repoUrl: string, blobId: string;

  beforeAll(() => {
    let run = shelljs.exec(
      `npm run env cli gistpen repo create -- --description='Test' --status='publish' --porcelain`,
      { silent: true }
    );

    if (run.code !== 0) {
      throw new Error(`Failed creating repo with error: ${run.stderr}`);
    }

    repoId =
      run.stdout
        .split('\n')
        .filter(Boolean)
        .pop() ?? '';

    if (!repoId) {
      throw new Error(`Failed to parse repo id from output: ${run.stdout}`);
    }

    run = shelljs.exec(
      `npm run env cli gistpen repo get ${repoId} -- --field='html_url'`,
      { silent: true }
    );

    if (run.code !== 0) {
      throw new Error(`Failed creating repo with error: ${run.stderr}`);
    }

    repoUrl =
      run.stdout
        .split('\n')
        .filter(Boolean)
        .pop() ?? '';

    if (!repoUrl) {
      throw new Error(`Failed to parse repo url from output: ${run.stdout}`);
    }

    run = shelljs.exec(
      `npm run env cli gistpen blob create -- --repo_id=${repoId} --filename='placeholder' --porcelain`,
      { silent: true }
    );

    if (run.code !== 0) {
      throw new Error(`Failed creating blob with error: ${run.stderr}`);
    }

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

      const run = shelljs.exec(
        `npm run env cli gistpen blob update ${blobId} ${dockerPath} -- --filename='${slug}' --language='${slug}'`,
        { silent: true }
      );

      if (run.code !== 0) {
        throw new Error(`Failed updating blob with error: ${run.stderr}`);
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
