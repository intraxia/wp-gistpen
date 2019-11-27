declare module '@wordpress/e2e-test-utils' {
  export function activatePlugin(slug: string): Promise<void>;
  export function setBrowserViewport(
    type: 'small' | 'medium' | 'large'
  ): Promise<void>;
}
