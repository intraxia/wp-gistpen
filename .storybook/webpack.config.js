const { eslintRule, styleRule, usableStyleRule,
    flowPlugin, styleLintPlugin, notifierPlugin, resolve,
    prismLanguageGenerationPlugin } = require('../webpack');

module.exports = {
    module: {
        rules: [
            eslintRule,
            styleRule,
            usableStyleRule
        ]
    },
    resolve,
    plugins: [
        flowPlugin,
        styleLintPlugin,
        notifierPlugin,
        prismLanguageGenerationPlugin
    ]
};
