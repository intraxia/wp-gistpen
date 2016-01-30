<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   WP_Gistpen
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
			'show_on'    => array( 'key' => 'options-page', 'value' => array( 'wp-gistpen' ) ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Add your GitHub token', 'wp-gistpen' ),
					'desc' => '<a href="https://github.com/settings/tokens/new">' . __( 'Create a GitHub token', 'wp-gistpen' ) . '</a>',
					'id'   => $prefix . 'gist_token',
					'type' => 'text',
				),
				array(
					'name' => __( 'Highlighter Theme', 'wp-gistpen' ),
					'desc' => __( 'This is the theme PrismJS highlights your code with. See how it works below.', 'wp-gistpen' ),
					'id'   => $prefix . 'gistpen_highlighter_theme',
					'type' => 'select',
					'options' => array(
						'default' => __( 'Default', 'wp-gistpen' ),
						'dark' => __( 'Dark', 'wp-gistpen' ),
						'funky' => __( 'Funky', 'wp-gistpen' ),
						'okaidia' => __( 'Okaidia', 'wp-gistpen' ),
						'tomorrow' => __( 'Tomorrow', 'wp-gistpen' ),
						'twilight' => __( 'Twilight', 'wp-gistpen' ),
						'coy' => __( 'Coy', 'wp-gistpen' ),
						'cb' => __( 'CB', 'wp-gistpen' ),
						'ghcolors' => __( 'GHColors', 'wp-gistpen' ),
						'pojoaque' => __( 'Projoaque', 'wp-gistpen' ),
						'xonokai' => __( 'Xonokai', 'wp-gistpen' ),
						'base16-ateliersulphurpool.light' => __( 'Ateliersulphurpool-Light', 'wp-gistpen' ),
						'hopscotch' => __( 'Hopscotch', 'wp-gistpen' ),
						'atom-dark' =>__( 'Atom Dark', 'wp-gistpen' ),
					),
				),
				array(
					'name' => __( 'Enable line numbers', 'wp-gistpen' ),
					'id'   => $prefix . 'gistpen_line_numbers',
					'type' => 'checkbox',
				),
				array(
					'name' => __( 'Enable show invisibles', 'wp-gistpen' ),
					'id'   => $prefix . 'show_invisibles',
					'type' => 'checkbox',
				),
			)
		), 'wp-gistpen' );
	?>

	<pre class="gistpen line-numbers" data-edit-url="#" data-filename="demo.rb"><code class="language-ruby"># Simple for loop using a range.
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
