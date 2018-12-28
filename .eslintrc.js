module.exports = {
    root: true,
    parser: 'babel-eslint',
    parserOptions: {
        ecmaVersion: 6,
        sourceType: 'module',
        ecmaFeatures: {
            impliedStrict: true,
            experimentalObjectRestSpread: true,
            jsx: true
        },
    },
    globals: {
        '__webpack_public_path__': true
    },
    env: {
        es6: true,
        node: true,
        browser: true
    },
    plugins: [
        'flowtype',
        'react',
    ],
    extends: [
        'valtech'
    ],
    rules: {
        // default rules overrides
        'eqeqeq': [2, "smart"],
        'rest-spread-spacing': [2, 'never'],

        // flowtype
        'flowtype/define-flow-type': 'error',

        // react
        'react/jsx-uses-react': 2,
        'react/jsx-uses-vars': 2,
    },
    settings: {
        flowtype: {
            'onlyFilesWithFlowAnnotation': true
        },
        react: {
            pragma: 'h',
        }
    }
};
