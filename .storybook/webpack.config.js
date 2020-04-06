const {
  lazyStyleRule,
  styleLintPlugin,
  notifierPlugin,
  prismLanguageGenerationPlugin
} = require('../webpack');

module.exports = ({ config }) => {
  config.module.rules.splice(2, 1);
  config.module.rules[0].test = /\.(tsx?|mjs|jsx?)$/;
  config.module.rules.push(lazyStyleRule);
  config.plugins.push(
    styleLintPlugin,
    notifierPlugin,
    prismLanguageGenerationPlugin
  );

  return config;
};
