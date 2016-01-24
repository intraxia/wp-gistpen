var Prism = require('./prism');
var $ = require('jquery');
var _ = require('underscore');

$(document).ready(function () {
    var themeSelect;
    var lnSelect;
    var pre;
    var lineNumbers;
    var exportBtn;
    var importBtn;
    var siSelect;

    Prism.highlightAll();

    queryDOM();
    Prism.loadTheme(Gistpen_Settings.prism.theme);
    toggleLineNumbers(Gistpen_Settings.prism.plugins['line-numbers'].enabled);
    toggleInvisibles(Gistpen_Settings.prism.plugins['show-invisibles'].enabled);
    setClickHandlers();

    function queryDOM() {
        themeSelect = $('#_wpgp_gistpen_highlighter_theme');
        lnSelect = $('#_wpgp_gistpen_line_numbers');
        siSelect = $('#_wpgp_show_invisibles');
        pre = $('pre.gistpen');
        code = pre.find('code');
        lineNumbers = $('span.line-numbers-rows');
        exportBtn = $('#export-gistpens');
        importBtn = $('#import-gists');
    }

    function setClickHandlers() {
        themeSelect.change(function () {
            Prism.loadTheme(themeSelect.val());
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

    function toggleInvisibles(enable) {

    }
});
