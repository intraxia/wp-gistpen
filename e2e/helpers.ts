import path from 'path';
import execa from 'execa';
import { openGlobalBlockInserter } from '@wordpress/e2e-test-utils';
import { Dialog } from 'puppeteer';

export const resetSite = () => execa(path.join(__dirname, 'reset-site.sh'));

export const insertBlock = async (searchTerm: string) => {
  await openGlobalBlockInserter();
  await page.waitFor('.block-editor-inserter__search');
  await page.type('.block-editor-inserter__search', searchTerm);
  const insertButton = (
    await page.$x(`//button//span[contains(text(), '${searchTerm}')]`)
  )[0];
  await insertButton.click();
};

export const acceptDialog = async (dialog: Dialog) => {
  await dialog.accept();
};

const jsDockerPath = `/var/www/src/wp-content/plugins/wp-gistpen/resources/samples/js`;

export const createGistpen = async ({ description = 'Test', filename = 'js' } = {}) => {
  let run = await execa.command(
    `npm run env cli gistpen repo create -- --description='${description}' --status='publish' --porcelain`,
  );
  let repoId =
    run.stdout
      .split('\n')
      .filter(Boolean)
      .pop() ?? '';

  if (!repoId) {
    throw new Error(`Failed to parse repo id from output: ${run.stdout}`);
  }

  run = await execa.command(
    `npm run env cli gistpen repo get ${repoId} -- --field='html_url'`,
  );

  let repoUrl =
    run.stdout
      .split('\n')
      .filter(Boolean)
      .pop() ?? '';

  if (!repoUrl) {
    throw new Error(`Failed to parse repo url from output: ${run.stdout}`);
  }

  run = await execa.command(
    process.env.SKIP_IMAGE_TESTS !== 'true'
      ? `npm run env cli gistpen blob create -- --repo_id=${repoId} --filename='placeholder' --porcelain`
      : `npm run env cli gistpen blob create ${jsDockerPath}  -- --repo_id=${repoId} --filename='${filename}' --language='js' --porcelain`,
  );

  let blobId =
    run.stdout
      .split('\n')
      .filter(Boolean)
      .pop() ?? '';

  if (!blobId) {
    throw new Error(`Failed to parse blob id from output: ${run.stdout}`);
  }

  return { repoId, repoUrl, blobId };
};
