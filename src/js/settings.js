var $ = require('jquery');
var _ = require('underscore');

document.addEventListener('DOMContentLoaded', function () {
    var source = document.querySelector('.gistpen');
    var wrap = document.querySelector('.wpgp-wrap');

    /**
     * Remove the code from the page.
     * We'll hold onto it and inject a clone into
     * the iframe for rendering. Holding it in memory
     * allows us to reuse it betweetn iframe injections,
     * so we can cleanly teardown and re-inject the iframe
     *
     */
    wrap.removeChild(source);

    /**
     * Add click handlers.
     */
    var themeSelect = document.getElementById('_wpgp_gistpen_highlighter_theme');
    themeSelect.addEventListener('change', updateTheme);

    /**
     * Update the theme and reinject the iframe.
     */
    function updateTheme() {
        Gistpen_Settings.prism.theme = themeSelect.value;

        inject();
    }

    var lineNumbersCheckbox = document.getElementById('_wpgp_gistpen_line_numbers');
    lineNumbersCheckbox.addEventListener('change', updateLineNumbers);

    /**
     * Update whether line numbers are enabled and reinject the iframe.
     */
    function updateLineNumbers() {
        var enabled = Gistpen_Settings.prism.plugins['line-numbers'].enabled = lineNumbersCheckbox.checked;

        if (enabled) {
            source.classList.add('line-numbers');
        } else {
            source.classList.remove('line-numbers');
        }

        inject();
    }

    var showInvisiblesCheckbox = document.getElementById('_wpgp_show_invisibles');
    showInvisiblesCheckbox.addEventListener('change', updateShowInvisibles);

    /**
     * Update whether invisibles are shown and reinject the iframe.
     */
    function updateShowInvisibles() {
        Gistpen_Settings.prism.plugins['show-invisibles'].enabled = showInvisiblesCheckbox.checked;

        inject();
    }

    inject();

    /**
     * Generate and inject the iframe into the DOM.
     */
    function inject() {
        /**
         * Tear down the previous iframe, if it exists.
         */
        var oldIframe = document.querySelector('.gistpen');

        if (oldIframe) {
            wrap.removeChild(oldIframe);
        }

        /**
         * Create our new iframe.
         */
        var iframe = document.createElement('iframe');
        iframe.classList.add('gistpen');

        /**
         * Update the DOM.
         *
         * We need to do this here, rather than after we've
         * injected all the pieces, because iframe.contentWindow
         * isn't available until the iframe has been injected
         * into the page.
         */
        wrap.appendChild(iframe);

        /**
         * Read out data and build iframe.
         */
        var min = "1" === Gistpen_Settings.debug ? '' : '.min';
        var web = document.createElement('script');
        web.src = Gistpen_Settings.url + 'assets/js/web' + min + '.js';
        web.async = true;

        iframe.contentWindow.Gistpen_Settings = Gistpen_Settings;
        iframe.contentWindow.document.body.appendChild(source.cloneNode(true));
        iframe.contentWindow.document.body.appendChild(web);
    }
});
