<?php
/**
 * Renders the Blob content.
 *
 * @package Intraxia\Gistpen
 * @var string
 */

return sprintf(
	'<pre class="gistpen%s"%s%s%s%s data-filename="%s"><code class="language-%s">%s</code></pre>',
	isset( $data['prism']['line-numbers'] ) && $data['prism']['line-numbers'] ? ' line-numbers' : '',
	isset( $data['blob']['highlight'] ) && $data['blob']['highlight'] ? ' data-line="' . $data['blob']['highlight'] . '"' : '',
	isset( $data['blob']['offset'] ) && $data['blob']['offset'] ? ' data-line-offset="' . $data['blob']['offset'] . '"' : '',
	isset( $data['blob']['offset'] ) && $data['blob']['offset'] ? ' data-start="' . ( $data['blob']['offset'] + 1 ) . '"' : '',
	isset( $data['blob']['edit_url'] ) && $data['blob']['edit_url'] ? ' data-edit-url="' . $data['blob']['edit_url'] . '"' : '',
	htmlspecialchars( $data['blob']['filename'] ),
	$this->prism_slug( $data['blob']['language']['slug'] ),
	htmlspecialchars( $data['blob']['code'] )
);
