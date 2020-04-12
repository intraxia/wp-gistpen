const {
  lazyStyleRule,
  styleLintPlugin,
  notifierPlugin,
  prismLanguageGenerationPlugin
} = require('../webpack');

module.exports = {
  stories: ['../client/**/__stories__/*.stories.tsx'],
  addons: [
    '@storybook/addon-actions',
    '@storybook/addon-links',
    'brookjs-desalinate/register'
  ],
  webpack: async config => {
    config.module.rules.splice(2, 1);
    config.module.rules[0].test = /\.(tsx?|mjs|jsx?)$/;
    config.module.rules.push(lazyStyleRule);
    config.plugins.push(
      styleLintPlugin,
      notifierPlugin,
      prismLanguageGenerationPlugin
    );
  
    return config;
  }
};