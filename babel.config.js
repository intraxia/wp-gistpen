module.exports = api => {
  api.cache(true);

  return {
    presets: [
      'brookjs',
      '@babel/typescript',
      ['@babel/env', { modules: false }]
    ],
    plugins: [
      '@babel/syntax-dynamic-import',
      process.env.NODE_ENV === 'test' && '@babel/transform-modules-commonjs'
    ].filter(Boolean)
  };
};
