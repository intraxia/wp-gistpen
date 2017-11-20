module.exports = {
    root: true,
    parser: 'babel-eslint',
    parserOptions: {
        ecmaVersion: 6,
        sourceType: 'module',
        ecmaFeatures: {
            impliedStrict: true,
            experimentalObjectRestSpread: true
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
        'flowtype'
    ],
    extends: [
        'valtech'
    ],
    rules: {
        'eqeqeq': [2, "smart"],
        'rest-spread-spacing': [2, 'never'],
        'flowtype/boolean-style': ['error', 'boolean'],
        'flowtype/define-flow-type': 'error',
        'flowtype/delimiter-dangle': ['error', 'always'],
        'flowtype/generic-spacing': ['error', 'never'],
        'flowtype/no-weak-types': 'warn',
        'flowtype/require-parameter-type': 'warn',
        'flowtype/require-return-type': ['error', 'always', {
            'excludeArrowFunctions': 'expressionsOnly',
            'annotateUndefined': 'never'
        }],
        'flowtype/require-valid-file-annotation': 'error',
        'flowtype/semi': ['error', 'always'],
        'flowtype/space-after-type-colon': ['error', 'always'],
        'flowtype/space-before-generic-bracket': ['error', 'never'],
        'flowtype/space-before-type-colon': ['error', 'always'],
        'flowtype/union-intersection-spacing': ['error', 'always'],
        'flowtype/use-flow-type': 'error',
        'flowtype/valid-syntax': 'error'
    },
    settings: {
        flowtype: {
            'onlyFilesWithFlowAnnotation': true
        }
    }
};
