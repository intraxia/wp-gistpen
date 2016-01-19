var Prism = require('prismjs/components/prism-core');
require('prismjs/components/prism-clike');
require('prismjs/components/prism-ruby');
document.removeEventListener('DOMContentLoaded', Prism.highlightAll);

var Preview = require('./settings/preview');
var Export = require('./settings/export');
var Import = require('./settings/import');
var $ = require('jquery');

$(document).ready(function () {
    Prism.highlightAll(true);
    Preview.init();

    Export.init();
    Import.init();
});
