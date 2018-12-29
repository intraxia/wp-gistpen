const { eslintRule, styleRule, usableStyleRule,
    styleLintPlugin, notifierPlugin, resolve,
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
        styleLintPlugin,
        notifierPlugin,
        prismLanguageGenerationPlugin
    ]
};
