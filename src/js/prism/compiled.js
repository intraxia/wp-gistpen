var Prism = require('prismjs/components/prism-core');
require('prismjs/components/prism-markup');
require('prismjs/components/prism-css');
require('prismjs/components/prism-clike');
require('prismjs/components/prism-javascript');
require('prismjs/components/prism-php');
require('prismjs/components/prism-bash');
require('prismjs/components/prism-groovy');
require('prismjs/components/prism-java');
require('prismjs/components/prism-python');
require('prismjs/components/prism-ruby');
require('prismjs/components/prism-scala');
require('prismjs/components/prism-scss');
require('prismjs/components/prism-sql');
// New languages - v0.3.0
require('prismjs/components/prism-c');
require('prismjs/components/prism-coffeescript');
require('prismjs/components/prism-csharp');
require('prismjs/components/prism-go');
require('prismjs/components/prism-http');
require('prismjs/components/prism-ini');
require('prismjs/components/prism-markup');
require('prismjs/components/prism-objectivec');
require('prismjs/components/prism-swift');
require('prismjs/components/prism-twig');
// New languages - v0.5.0
require('prismjs/components/prism-actionscript');
require('prismjs/components/prism-applescript');
require('prismjs/components/prism-dart');
require('prismjs/components/prism-eiffel');
require('prismjs/components/prism-erlang');
require('prismjs/components/prism-gherkin');
require('prismjs/components/prism-git');
require('prismjs/components/prism-haml');
require('prismjs/components/prism-handlebars');
require('prismjs/components/prism-jade');
require('prismjs/components/prism-latex');
require('prismjs/components/prism-less');
require('prismjs/components/prism-markdown');
require('prismjs/components/prism-matlab');
require('prismjs/components/prism-nasm');
require('prismjs/components/prism-perl');
require('prismjs/components/prism-powershell');
require('prismjs/components/prism-r');
require('prismjs/components/prism-rust');
require('prismjs/components/prism-scheme');
require('prismjs/components/prism-smarty');
// Plugins
require('prismjs/plugins/line-highlight/prism-line-highlight');
require('prismjs/plugins/line-numbers/prism-line-numbers');
require('prismjs/plugins/show-invisibles/prism-show-invisibles');
require('prismjs/plugins/show-language/prism-show-language');

document.removeEventListener('DOMContentLoaded', Prism.highlightAll);

module.exports = Prism;
