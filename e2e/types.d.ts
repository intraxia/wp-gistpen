declare module '@wordpress/e2e-test-utils' {
  import { ElementHandle, Request } from 'puppeteer';

  // @TODO(mAAdhaTTah) upstream to DTyped

  /**
   * Activates an installed plugin.
   *
   * @param {string} slug Plugin slug.
   */
  export function activatePlugin(slug: string): Promise<void>;

  /**
   * Verifies if publish checks are enabled.
   *
   * @return {boolean} Boolean which represents the state of prepublish checks.
   */
  export function arePrePublishChecksEnabled(): Promise<boolean>;

  /**
   * Clears the local storage.
   */
  export function clearLocalStorage(): Promise<void>;

  /**
   * Clicks the default block appender.
   */
  export function clickBlockAppender(): Promise<void>;

  /**
   * Clicks a block toolbar button.
   *
   * @param {string} buttonAriaLabel The aria label of the button to click.
   */
  export function clickBlockToolbarButton(
    buttonAriaLabel: string
  ): Promise<void>;

  /**
   * Clicks a button based on the text on the button.
   *
   * @param {string} buttonText The text that appears on the button to click.
   */
  export function clickButton(buttonText: string): Promise<void>;

  /**
   * Click on the close button of an open modal.
   *
   * @param {?string} modalClassName Class name for the modal to close
   */
  export function clickOnCloseModalButton(
    modalClassName?: string
  ): Promise<void>;

  /**
   * Clicks on More Menu item, searches for the button with the text provided and clicks it.
   *
   * @param {string} buttonLabel The label to search the button for.
   */
  export function clickOnMoreMenuItem(buttonLabel: string): Promise<void>;

  /**
   * Creates new post.
   *
   * @param {Object} obj Object to create new post, along with tips enabling option.
   */
  export function createNewPost(obj?: {
    postType: string;
    title: string;
    content: string;
    excerpt: string;
    enableTips?: boolean;
    showWelcomeGuide?: boolean;
  }): Promise<void>;

  /**
   * Creates new URL by parsing base URL, WPPath and query string.
   *
   * @param {string} WPPath String to be serialized as pathname.
   * @param {?string} query String to be serialized as query portion of URL.
   * @return {string} String which represents full URL.
   */
  export function createURL(WPPath: string, query?: string): string;

  /**
   * Deactivates an active plugin.
   *
   * @param {string} slug Plugin slug.
   */
  export function deactivatePlugin(slug: string): Promise<void>;

  /**
   * Disables Pre-publish checks.
   */
  export function disablePrePublishChecks(): Promise<void>;

  /**
   * Clicks an element, drags a particular distance and releases the mouse button.
   *
   * @param {Object} element The puppeteer element handle.
   * @param {Object} delta   Object containing movement distances.
   * @param {number} delta.x Horizontal distance to drag.
   * @param {number} delta.y Vertical distance to drag.
   *
   * @return {Promise} Promise resolving when drag completes.
   */
  export function dragAndResize(
    element: ElementHandle,
    delta: { x: number; y: number }
  ): Promise<void>;

  /**
   * Enables even listener which accepts a page dialog which
   * may appear when navigating away from Gutenberg.
   */
  export function enablePageDialogAccept(): Promise<void>;

  /**
   * Enables Pre-publish checks.
   */
  export function enablePrePublishChecks(): Promise<void>;

  /**
   * Verifies that the edit post sidebar is opened, and if it is not, opens it.
   *
   * @return {Promise} Promise resolving once the edit post sidebar is opened.
   */
  export function ensureSidebarOpened(): Promise<void>;

  /**
   * Finds a sidebar panel with the provided title.
   *
   * @param {string} panelTitle The name of sidebar panel.
   *
   * @return {?ElementHandle} Object that represents an in-page DOM element.
   */
  export function findSidebarPanelToggleButtonWithTitle(
    panelTitle: string
  ): Promise<ElementHandle | undefined>;

  /**
   * Finds the button responsible for toggling the sidebar panel with the provided title.
   *
   * @param {string} panelTitle The name of sidebar panel.
   *
   * @return {Promise<ElementHandle|undefined>} Object that represents an in-page DOM element.
   */
  export function findSidebarPanelWithTitle(
    panelTitle: string
  ): Promise<ElementHandle | undefined>;

  /**
   * Returns an array of strings with all inserter item titles.
   *
   * @return {Promise} Promise resolving with an array containing all inserter item titles.
   */
  export function getAllBlockInserterItemTitles(): Promise<void>;

  /**
   * Returns an array with all blocks; Equivalent to calling wp.data.select( 'core/block-editor' ).getBlocks();
   *
   * @return {Promise} Promise resolving with an array containing all blocks in the document.
   */
  export function getAllBlocks(): Promise<Array<Object>>;

  /**
   * Returns an array of strings with all block titles,
   * that the current selected block can be transformed into.
   *
   * @return {Promise} Promise resolving with an array containing all possible block transforms
   */
  export function getAvailableBlockTransforms(): Promise<Array<Object>>;

  /**
   * Returns a string containing the block title associated with the provided block name.
   *
   * @param {string} blockName Block name.
   * @param {string} setting   Block setting e.g: title, attributes....
   *
   * @return {Promise} Promise resolving with a string containing the block title.
   */
  export function getBlockSetting(
    blockName: string,
    setting: string
  ): Promise<string>;

  /**
   * Returns a promise which resolves with the edited post content (HTML string).
   *
   * @return {Promise} Promise resolving with post content markup.
   */
  export function getEditedPostContent(): Promise<string>;

  /**
   * Returns a boolean indicating if the current selected block has a block switcher or not.
   *
   * @return {Promise} Promise resolving with a boolean.
   */
  export function hasBlockSwitcher(): Promise<boolean>;

  /**
   * Opens the inserter, searches for the given term, then selects the first
   * result that appears.
   *
   * @param {string} searchTerm The text to search the inserter for.
   * @param {string} panelName  The inserter panel to open (if it's closed by default).
   */
  export function insertBlock(
    searchTerm: string,
    panelName?: string
  ): Promise<void>;

  /**
   * Installs a plugin from the WP.org repository.
   *
   * @param {string} slug        Plugin slug.
   * @param {string?} searchTerm If the plugin is not findable by its slug use an alternative term to search.
   */
  export function installPlugin(
    slug: string,
    searchTerm?: string
  ): Promise<void>;

  /**
   * Checks if current URL is a WordPress path.
   *
   * @param {string} WPPath String to be serialized as pathname.
   * @param {?string} query String to be serialized as query portion of URL.
   * @return {boolean} Boolean represents whether current URL is or not a WordPress path.
   */
  export function isCurrentURL(
    WPPath: string,
    query?: string
  ): Promise<boolean>;

  /**
   * Checks if the block that is focused is the default block.
   *
   * @return {Promise} Promise resolving with a boolean indicating if the focused block is the default block.
   */
  export function isInDefaultBlock(): Promise<boolean>;

  /**
   * Performs log in with specified username and password.
   *
   * @param {?string} username String to be used as user credential.
   * @param {?string} password String to be used as user credential.
   */
  export function loginUser(
    username?: string,
    password?: string
  ): Promise<void>;

  /**
   * Adds an event listener to the document which throws an error if there is a
   * loss of focus.
   */
  export function enableFocusLossObservation(): Promise<void>;

  /**
   * Removes the focus loss listener that `enableFocusLossObservation()` adds.
   */
  export function disableFocusLossObservation(): Promise<void>;

  /**
   * Opens all block inserter categories.
   */
  export function openAllBlockInserterCategories(): Promise<void>;

  /**
   * Clicks on the button in the header which opens Document Settings sidebar when it is closed.
   */
  export function openDocumentSettingsSidebar(): Promise<void>;

  /**
   * Opens the global block inserter.
   */
  export function openGlobalBlockInserter(): Promise<void>;

  /**
   * Opens the publish panel.
   */
  export function openPublishPanel(): Promise<void>;

  /**
   * Presses the given keyboard key a number of times in sequence.
   *
   * @param {string} key   Key to press.
   * @param {number} count Number of times to press.
   */
  export function pressKeyTimes(key: string, count: number): Promise<void>;

  /**
   * Emulates a Ctrl+A SelectAll key combination by dispatching custom keyboard
   * events and using the results of those events to determine whether to call
   * `document.execCommand( 'selectall' );`. This is necessary because Puppeteer
   * does not emulate Ctrl+A SelectAll in macOS. Events are dispatched to ensure
   * that any `Event#preventDefault` which would have normally occurred in the
   * application as a result of Ctrl+A is respected.
   *
   * @see https://github.com/GoogleChrome/puppeteer/issues/1313
   * @see https://w3c.github.io/uievents/tools/key-event-viewer.html
   *
   * @return {Promise} Promise resolving once the SelectAll emulation completes.
   */
  export function pressKeyWithModifier(modifier: string, key: string): Promise<void>;

  /**
   * Publishes the post, resolving once the request is complete (once a notice
   * is displayed).
   *
   * @return {Promise} Promise resolving when publish is complete.
   */
  export function publishPost(): Promise<void>;

  /**
   * Publishes the post without the pre-publish checks,
   * resolving once the request is complete (once a notice is displayed).
   *
   * @return {Promise} Promise resolving when publish is complete.
   */
  export function publishPostWithPrePublishChecksDisabled(): Promise<void>;

  /**
   * Saves the post as a draft, resolving once the request is complete (once the
   * "Saved" indicator is displayed).
   *
   * @return {Promise} Promise resolving when draft save is complete.
   */
  export function saveDraft(): Promise<void>;

  /**
   * Search for block in the global inserter
   *
   * @param {string} searchTerm The text to search the inserter for.
   */
  export function searchForBlock(searchTerm: string): Promise<void>;

  /**
   * Given the clientId of a block, selects the block on the editor.
   *
   * @param {string} clientId Identified of the block.
   */
  export function selectBlockByClientId(clientId: string): Promise<void>;

  /**
   * Named viewport options.
   */
  export type WPDimensionsName = 'small' | 'medium' | 'large';

  /**
   * Viewport dimensions object.
   */
  export type WPViewportDimensions = { width: number; height: number };

  /**
   * Valid argument argument type from which to derive viewport dimensions.
   */
  export type WPViewport = WPDimensionsName | WPViewportDimensions;

  /**
   * Sets browser viewport to specified type.
   *
   * @param {WPViewport} viewport Viewport name or dimensions object to assign.
   */
  export function setBrowserViewport(viewport: WPViewport): Promise<void>;

  /**
   * Sets code editor content
   *
   * @param {string} content New code editor content.
   *
   * @return {Promise} Promise resolving with an array containing all blocks in the document.
   */
  export function setPostContent(content: string): Promise<void>;

  /**
   * Switches editor mode.
   *
   * @param {string} mode String editor mode.
   */
  export function switchEditorModeTo(mode: string): Promise<void>;

  /**
   * Switches the current user to the admin user (if the user
   * running the test is not already the admin user).
   */
  export function switchUserToAdmin(): Promise<void>;

  /**
   * Switches the current user to whichever user we should be
   * running the tests as (if we're not already that user).
   */
  export function switchUserToTest(): Promise<void>;

  /**
   * Toggles the More Menu.
   */
  export function toggleMoreMenu(): Promise<void>;

  /**
   * Toggle the page into offline mode.
   *
   * @param {boolean} isOffline
   */
  export function toggleOfflineMode(isOffline: boolean): Promise<void>;

  /**
   * Whether offline mode is enabled.
   *
   * @return boolean
   */
  export function isOfflineMode(): boolean;

  /**
   * Toggles the screen option with the given label.
   *
   * @param {string}   label           The label of the screen option, e.g. 'Show Tips'.
   * @param {?boolean} shouldBeChecked If true, turns the option on. If false, off. If
   *                                   undefined, the option will be toggled.
   */
  export function toggleScreenOption(
    label: string,
    shouldBeChecked?: boolean
  ): Promise<void>;

  /**
   * Converts editor's block type.
   *
   * @param {string} name Block name.
   */
  export function transformBlockTo(name: string): Promise<void>;

  /**
   * Uninstalls a plugin.
   *
   * @param {string} slug Plugin slug.
   */
  export function uninstallPlugin(slug: string): Promise<void>;

  /**
   * Visits admin page; if user is not logged in then it logging in it first, then visits admin page.
   *
   * @param {string} adminPath String to be serialized as pathname.
   * @param {string} query String to be serialized as query portion of URL.
   */
  export function visitAdminPage(
    adminPath: string,
    query: string
  ): Promise<void>;

  /**
   * Function that waits until the page viewport has the required dimensions.
   * It is being used to address a problem where after using setViewport the execution may continue,
   * without the new dimensions being applied.
   * https://github.com/GoogleChrome/puppeteer/issues/1751
   *
   * @param {number} width  Width of the window.
   * @param {number} height Height of the window.
   */
  export function waitForWindowDimensions(
    width: number,
    height: number
  ): Promise<void>;

  /**
   * Creates a function to determine if a request is calling a URL with the substring present.
   *
   * @param {string} substring The substring to check for.
   * @return {Function} Function that determines if a request's URL contains substring.
   */
  export function createURLMatcher(
    substring: string
  ): (request: Request) => boolean;

  /**
   * Creates a function to determine if a request is embedding a certain URL.
   *
   * @param {string} url The URL to check against a request.
   * @return {Function} Function that determines if a request is for the embed API, embedding a specific URL.
   */
  export function createEmbeddingMatcher(
    url: string
  ): (request: Request) => boolean;

  /**
   * Respond to a request with a JSON response.
   *
   * @param {string} mockResponse The mock object to wrap in a JSON response.
   * @return {Promise} Promise that responds to a request with the mock JSON response.
   */
  export function createJSONResponse(
    mockResponse: string
  ): (request: Request) => Promise<void>;

  /**
   * Mocks a request with the supplied mock object, or allows it to run with an optional transform, based on the
   * deserialised JSON response for the request.
   *
   * @param {Function} mockCheck function that returns true if the request should be mocked.
   * @param {Object} mock A mock object to wrap in a JSON response, if the request should be mocked.
   * @param {Function|undefined} responseObjectTransform An optional function that transforms the response's object before the response is used.
   * @return {Promise} Promise that uses `mockCheck` to see if a request should be mocked with `mock`, and optionally transforms the response with `responseObjectTransform`.
   */
  export function mockOrTransform(
    mockCheck: Function,
    mock: Object,
    responseObjectTransform?: Function
  ): (request: Request) => Promise<void>;

  /**
   * Sets up mock checks and responses. Accepts a list of mock settings with the following properties:
   *
   * - `match`: function to check if a request should be mocked.
   * - `onRequestMatch`: async function to respond to the request.
   *
   * @example
   *
   * ```js
   * const MOCK_RESPONSES = [
   *   {
   *     match: isEmbedding( 'https://wordpress.org/gutenberg/handbook/' ),
   *     onRequestMatch: JSONResponse( MOCK_BAD_WORDPRESS_RESPONSE ),
   *   },
   *   {
   *     match: isEmbedding( 'https://wordpress.org/gutenberg/handbook/block-api/attributes/' ),
   *     onRequestMatch: JSONResponse( MOCK_EMBED_WORDPRESS_SUCCESS_RESPONSE ),
   *   }
   * ];
   * setUpResponseMocking( MOCK_RESPONSES );
   * ```
   *
   * If none of the mock settings match the request, the request is allowed to continue.
   *
   * @param {Array} mocks Array of mock settings.
   */
  export function setUpResponseMocking(
    mocks: Array<{
      match: (request: Request) => boolean;
      onRequestMatch: (request: Request) => Promise<void>;
    }>
  ): void;
}
