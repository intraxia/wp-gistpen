<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Intraxia\Gistpen
 * @author    James DiGioia <jamesorodig@gmail.com>
 * @license   GPL-2.0+
 * @link      http://jamesdigioia.com/wp-gistpen/
 * @copyright 2014 James DiGioia
 */
?>

<?php do_action( 'wpgp_settings_before_title' ); ?>

<div class="wpgp-wrap">

<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php

		$prefix = '_wpgp_';

		cmb2_metabox_form( array(
			'id'         => 'wpgp_option_metabox',
			'show_on'    => array( 'key' => 'options-page', 'value' => array( \Gistpen::$plugin_name ) ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Add your GitHub token', \Gistpen::$plugin_name ),
					'desc' => '<a href="https://github.com/settings/tokens/new">' . __( 'Create a GitHub token', \Gistpen::$plugin_name ) . '</a>',
					'id'   => $prefix . 'gist_token',
					'type' => 'text',
				),
				array(
					'name' => __( 'Highlighter Theme', \Gistpen::$plugin_name ),
					'desc' => __( 'This is the theme PrismJS highlights your code with. See how it works below.', \Gistpen::$plugin_name ),
					'id'   => $prefix . 'gistpen_highlighter_theme',
					'type' => 'select',
					'options' => array(
						'default' => __( 'Default', \Gistpen::$plugin_name ),
						'dark' => __( 'Dark', \Gistpen::$plugin_name ),
						'funky' => __( 'Funky', \Gistpen::$plugin_name ),
						'okaidia' => __( 'Okaidia', \Gistpen::$plugin_name ),
						'twilight' => __( 'Twilight', \Gistpen::$plugin_name ),
						'coy' => __( 'Coy', \Gistpen::$plugin_name ),
					),
				),
				array(
					'name' => __( 'Enable line numbers', \Gistpen::$plugin_name ),
					'id'   => $prefix . 'gistpen_line_numbers',
					'type' => 'checkbox',
				),
			)
		), \Gistpen::$plugin_name );
	?>

	<pre class="gistpen line-numbers"><code class="language-ruby"># Simple for loop using a range.
for i in (1..4)
    print i," "
end
print "\n"

for i in (1...4)
    print i," "
end
print "\n"

# Running through a list (which is what they do).
items = [ 'Mark', 12, 'goobers', 18.45 ]
for it in items
    print it, " "
end
print "\n"

# Go through the legal subscript values of an array.
for i in (0...items.length)
    print items[0..i].join(" "), "\n"
end</code></pre>

</div>

<?php include_once( 'export.inc.php' ); ?>
<?php include_once( 'import.inc.php' ); ?>
<?php include_once( 'status.inc.php' ); ?>
