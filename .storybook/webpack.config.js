const {
  eslintRule,
  styleRule,
  usableStyleRule,
  styleLintPlugin,
  notifierPlugin,
  resolve,
  tsRule,
  prismLanguageGenerationPlugin
} = require('../webpack');

module.exports = ({ config }) => {
  config.module.rules.splice(2, 1);
  config.module.rules.push(eslintRule, styleRule, usableStyleRule, tsRule);
  config.resolve.extensions.push(...resolve.extensions);
  config.plugins.push(
    styleLintPlugin,
    notifierPlugin,
    prismLanguageGenerationPlugin
  );

  return config;
};
