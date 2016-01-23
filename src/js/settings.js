var Prism = require('prismjs/components/prism-core');
require('prismjs/components/prism-clike');
require('prismjs/components/prism-ruby');
require('prismjs/plugins/line-numbers/prism-line-numbers');
document.removeEventListener('DOMContentLoaded', Prism.highlightAll);

var $ = require('jquery');
var _ = require('underscore');

$(document).ready(function () {
    var cssLink;
    var themeSelect;
    var lnSelect;
    var pre;
    var lineNumbers;
    var exportBtn;
    var importBtn;

    Prism.highlightAll();

    queryDOM();
    setTheme(Gistpen_Settings.prism.theme);
    toggleLineNumbers(Gistpen_Settings.prism.plugins['line-numbers'].enabled);
    setClickHandlers();

    function queryDOM() {
        cssLink = $("<link>", {
            rel: "stylesheet",
            type: "text/css"
        });
        cssLink.appendTo('head');
        themeSelect = $('#_wpgp_gistpen_highlighter_theme');
        lnSelect = $('#_wpgp_gistpen_line_numbers');
        pre = $('pre.gistpen');
        code = pre.find('code');
        lineNumbers = $('span.line-numbers-rows');
        exportBtn = $('#export-gistpens');
        importBtn = $('#import-gists');
    }

    function setTheme(theme) {
        if (theme == 'default') {
            theme = '';
        } else {
            theme = '-' + theme;
        }

        cssLink.attr('href', Gistpen_Settings.url + 'assets/css/prism/themes/prism' + theme + '.css');
    }

    function setClickHandlers() {
        themeSelect.change(function () {
            setTheme(themeSelect.val());
        });
        lnSelect.click(function () {
            toggleLineNumbers(lnSelect.is(':checked'))
        });
        importBtn.prop('disabled', true);
        exportBtn.prop('disabled', true);
    }

    function toggleLineNumbers(enable) {
        if (enable) {
            pre.addClass('line-numbers');
            lineNumbers.prependTo(code);
        } else {
            pre.removeClass('line-numbers');
            lineNumbers.remove();
        }
    }
});
