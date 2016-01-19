/**
 * Register plugin with TinyMCE.
 */
tinymce.create('tinymce.plugins.wp_gistpen', {init: require('./button')});
tinymce.PluginManager.add('wp_gistpen', tinymce.plugins.wp_gistpen);
