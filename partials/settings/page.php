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
			'show_on'    => array( 'key' => 'options-page', 'value' => array( $this->plugin_name ) ),
			'show_names' => true,
			'fields'     => array(
				array(
					'name' => __( 'Add your GitHub token', $this->plugin_name ),
					'desc' => '<a href="https://github.com/settings/tokens/new">' . __( 'Create a GitHub token', $this->plugin_name ) . '</a>',
					'id'   => $prefix . 'gist_token',
					'type' => 'text',
				),
				array(
					'name' => __( 'Highlighter Theme', $this->plugin_name ),
					'desc' => __( 'This is the theme PrismJS highlights your code with. See how it works below.', $this->plugin_name ),
					'id'   => $prefix . 'gistpen_highlighter_theme',
					'type' => 'select',
					'options' => array(
						'default' => __( 'Default', $this->plugin_name ),
						'dark' => __( 'Dark', $this->plugin_name ),
						'funky' => __( 'Funky', $this->plugin_name ),
						'okaidia' => __( 'Okaidia', $this->plugin_name ),
						'twilight' => __( 'Twilight', $this->plugin_name ),
						'coy' => __( 'Coy', $this->plugin_name ),
					),
				),
				array(
					'name' => __( 'Enable line numbers', $this->plugin_name ),
					'id'   => $prefix . 'gistpen_line_numbers',
					'type' => 'checkbox',
				),
			)
		), $this->plugin_name );
	?>

	<pre class="gistpen line-numbers"><code class="language-markup"><?php
echo esc_html(<<<EOL
<?xml version="1.0"?>
<response value="ok" xml:lang="en">
  <text>Ok</text>
  <comment html_allowed="true"/>
  <ns1:description><![CDATA[
  CDATA is <not> magical.
  ]]></ns1:description>
  <a></a> <a/>
</response>


<!DOCTYPE html>
<title>Title</title>

<style>body {width: 500px;}</style>

<script type="application/javascript">
  function \$init() {return true;}
</script>

<body>
  <p checked class="title" id='title'>Title</p>
  <!-- here goes the rest of the page -->
</body>
EOL
);
	?></code></pre>

</div>

<?php include_once( 'export.inc.php' ); ?>
<?php include_once( 'import.inc.php' ); ?>
<?php include_once( 'status.inc.php' ); ?>
