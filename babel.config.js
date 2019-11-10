module.exports = api => {
  // Getting an error in e2e tests with cache enabled...?
  api.cache(false);

  return {
    presets: [
      'brookjs',
      '@babel/typescript',

      [
        '@babel/env',
        process.env.NODE_ENV === 'test'
          ? {
              modules: 'commonjs',
              targets: {
                node: 'current'
              }
            }
          : {
              modules: false,
              targets: {
                esmodules: true
              }
            }
      ]
    ],
    plugins: [
      '@babel/plugin-proposal-class-properties',
      '@babel/syntax-dynamic-import',
      '@babel/plugin-proposal-optional-chaining',
      '@babel/plugin-proposal-nullish-coalescing-operator'
    ]
  };
};
