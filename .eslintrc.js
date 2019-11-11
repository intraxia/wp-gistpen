module.exports = {
  root: true,
  parser: '@typescript-eslint/parser',
  parserOptions: {
    project: './tsconfig.json',
    ecmaVersion: 6,
    sourceType: 'module',
    ecmaFeatures: {
      impliedStrict: true,
      experimentalObjectRestSpread: true,
      jsx: true
    }
  },
  globals: {
    __webpack_public_path__: true,
    page: true,
    jQuery: true
  },
  env: {
    es6: true,
    node: true,
    browser: true
  },
  plugins: ['react', '@typescript-eslint'],
  extends: ['valtech', 'prettier', 'prettier/@typescript-eslint'],
  rules: {
    // default rules overrides
    eqeqeq: [2, 'smart'],
    'rest-spread-spacing': [2, 'never'],

    // react
    'react/jsx-uses-react': 2,
    'react/jsx-uses-vars': 2,

    // These rules don't work well
    camelcase: 0,
    indent: 0,
    'no-array-constructor': 0,
    'no-unused-vars': 0,

    '@typescript-eslint/no-angle-bracket-type-assertion': 2,
    '@typescript-eslint/no-array-constructor': 2,
    '@typescript-eslint/no-namespace': 2,
    '@typescript-eslint/no-unused-vars': 2
  }
};
