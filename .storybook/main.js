const path = require('path');
const { App, webpack } = require('brookjs-cli');

const brookConfig = webpack.selectWebpackConfig({
  cmd: 'start',
  cwd: path.join(__dirname, '..'),
  env: 'development',
  extension: 'ts',
  watch: false,
  rc: App.create('beaver').getRC()
});

module.exports = {
  stories: ['../client/**/__stories__/*.stories.tsx'],
  addons: ['@storybook/addon-actions', 'brookjs-desalinate/register'],
  webpackFinal: async config => ({
    ...config,
    module: {
      ...config.module,
      rules: config.module.rules
        // Remove the default CSS handler.
        .filter(rule => !rule.test.source.includes('css'))
        // Replace built-in JS babel-loader rule w/ our babel-loader.
        .map(rule =>
          rule.test.source.includes('js') ? brookConfig.module.rules[1] : rule
        )
        // Add our custom rules, including built-in styles + user modified.
        .concat(...brookConfig.module.rules.slice(3))
    },
    // Make sure we resolve all extensions.
    resolve: {
      ...config.resolve,
      extensions: brookConfig.resolve.extensions
    },
    // Add user custom plugins.
    plugins: [...config.plugins, ...brookConfig.plugins.slice(5)]
  })
};
