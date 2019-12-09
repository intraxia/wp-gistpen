/* eslint-env jest */
import { createNewPost, insertBlock } from '@wordpress/e2e-test-utils';

const ADD_BTN_SEL = '[data-testid="block-add-btn"]';
const SEARCH_BTN_SEL = '[data-testid="block-search-btn"]';
const SEARCH_INPT_SEL = '[data-testid="block-search-input"]';
const searchResult = (slug: string) =>
  `[data-testid="block-search-result-${slug}"]`;

describe('block', () => {
  it('should add a block from an existing blob', async () => {
    await createNewPost({
      postType: 'post',
      title: 'Add existing blob',
      content: '',
      excerpt: ''
    });
    await insertBlock('gistpen', 'widgets');

    await expect(page).toClick(SEARCH_BTN_SEL);
    await expect(page).toFill(SEARCH_INPT_SEL, 'js');
    await expect(page).toClick(searchResult('js'));
  });
});
