var $ = require('jquery');
var cssLink;
var themeSelect;
var lnSelect;
var pre;
var lineNumbers;

function init() {
    queryDOM();
    setTheme(Gistpen_Settings.prism.theme);
    toggleLineNumbers();
    setClickHandlers();
}

function queryDOM() {
    cssLink = $("<link>", {
        rel: "stylesheet",
        type: "text/css"
    });
    cssLink.appendTo('head');
    themeSelect = $('#_wpgp_gistpen_highlighter_theme');
    lnSelect = $('#_wpgp_gistpen_line_numbers');
    pre = $('pre.gistpen');
    lineNumbers = $('span.line-numbers-rows');
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
    lnSelect.click(toggleLineNumbers);
}

function toggleLineNumbers() {
    if (lnSelect.is(':checked')) {
        pre.addClass('line-numbers');
        lineNumbers.prependTo('pre.gistpen code');
        lineNumbers.show();
    } else {
        pre.removeClass('line-numbers');
        lineNumbers.hide();
    }
}

module.exports = {
    init: init
};
