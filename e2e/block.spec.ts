/* eslint-env jest */
import { createNewPost, clickButton } from '@wordpress/e2e-test-utils';
import { getDocument, queries, wait } from 'pptr-testing-library';
import { acceptDialog, insertBlock, createGistpen } from './helpers';

describe('block', () => {
  beforeEach(() => {
    page.on('dialog', acceptDialog);
  });

  afterEach(() => {
    page.off('dialog', acceptDialog);
  });

  beforeEach(async () => {
    await createNewPost({
      postType: 'post',
      title: 'Test post',
      content: '',
      excerpt: '',
    });
  });

  it('should create a new gistpen with a new snippet', async () => {
    await insertBlock('Gistpen');

    const $document = await getDocument(page);

    await clickButton('Create new');
    await clickButton('Create new repo');

    const $descriptionInput = await queries.getByLabelText(
      $document,
      'Gistpen description',
    );
    await $descriptionInput.type('Test gistpen');

    const $filenameInput = await queries.getByLabelText(
      $document,
      'Snippet filename',
    );
    await $filenameInput.type('filename.js');

    await clickButton('Create repo');

    expect(await page.waitFor('[data-testid="edit-embed"')).not.toBeNull();
  });

  it('should choose existing snippet', async () => {
    const filename = `custom-gistpen-${Math.round(Math.random() * 100)}`;
    await createGistpen({ filename });
    await insertBlock('Gistpen');

    const $document = await getDocument(page);

    await clickButton('Choose from existing');

    const $descriptionInput = await queries.getByLabelText(
      $document,
      'Search snippets',
    );
    await $descriptionInput.type(filename);

    await wait(() => queries.getByText($document, filename));

    await clickButton('Select');

    expect(await page.waitFor('[data-testid="edit-embed"')).not.toBeNull();
  });

  it('should attach new snippet to existing gistpen', async () => {
    const description = `Gistpen${Math.round(Math.random() * 100)}`;
    await createGistpen({ description });
    await insertBlock('Gistpen');

    const $document = await getDocument(page);

    await clickButton('Create new');
    await clickButton('Add to existing repo');

    const $filenameInput = await queries.getByLabelText(
      $document,
      'Snippet filename',
    );
    await $filenameInput.type('filename.js');

    const $descriptionInput = await queries.getByLabelText(
      $document,
      'Search repos',
    );
    await $descriptionInput.type(description);

    await wait(() => queries.getByText($document, description));

    await clickButton('Select');

    expect(await page.waitFor('[data-testid="edit-embed"')).not.toBeNull();
  });
});
