const {
  eslintRule,
  styleRule,
  usableStyleRule,
  styleLintPlugin,
  notifierPlugin,
  resolve,
  prismLanguageGenerationPlugin
} = require('../webpack');

module.exports = ({ config }) => {
  config.module.rules.splice(2, 1);
  config.module.rules.push(eslintRule, styleRule, usableStyleRule);
  config.resolve.extensions.push(...resolve.extensions);
  config.plugins.push(
    styleLintPlugin,
    notifierPlugin,
    prismLanguageGenerationPlugin
  );

  return config;
};
