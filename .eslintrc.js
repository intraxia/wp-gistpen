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
        'flowtype/boolean-style': ['error', 'boolean'],
        'flowtype/define-flow-type': 'error',
        'flowtype/delimiter-dangle': ['error', 'never'],
        'flowtype/generic-spacing': ['error', 'never'],
        'flowtype/no-flow-fix-me-comments': 'warn',
        'flowtype/no-primitive-constructor-types': 'error',
        'flowtype/no-types-missing-file-annotation': 'error',
        'flowtype/object-type-delimiter': ['error', 'comma'],
        'flowtype/require-valid-file-annotation': 'error',
        'flowtype/semi': ['error', 'always'],
        'flowtype/space-after-type-colon': ['error', 'always'],
        'flowtype/space-before-generic-bracket': ['error', 'never'],
        'flowtype/space-before-type-colon': ['error', 'never'],
        'flowtype/union-intersection-spacing': ['error', 'always'],
        'flowtype/use-flow-type': 'error',

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
