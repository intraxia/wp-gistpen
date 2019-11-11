/* eslint-env jest */
import { activatePlugin } from '@wordpress/e2e-test-utils';

describe('activation', () => {
  it('should activate from the plugins page', async () => {
    await activatePlugin('wp-gistpen');
  });
});
