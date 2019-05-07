/* eslint-env mocha */
const path = require('path');
const fs = require('fs');
const { expect } = require('chai');

function defineTest(dirName, transformName) {
  describe(transformName, () => {
    it('transforms correctly', () => {
      runTest(dirName, transformName);
    });
  });
}

function runTest(dirName, transformName) {
  const fixtureDir = path.join(dirName, '..', '__testfixtures__');
  const inputPath = path.join(fixtureDir, transformName + '.input.js');
  const source = fs.readFileSync(inputPath, 'utf8');
  const expectedOutput = fs.readFileSync(
    path.join(fixtureDir, transformName + '.output.js'),
    'utf8'
  );
  // Assumes transform is one level up from __tests__ directory
  const module = require(path.join(dirName, '..', transformName + '.js'));
  runInlineTest(
    module,
    {
      path: inputPath,
      source
    },
    expectedOutput
  );
}

function runInlineTest(module, input, expectedOutput) {
  // Handle ES6 modules using default export for the transform
  const transform = module.default ? module.default : module;

  // Jest resets the module registry after each test, so we need to always get
  // a fresh copy of jscodeshift on every test run.
  let jscodeshift = require('jscodeshift');
  if (module.parser) {
    jscodeshift = jscodeshift.withParser(module.parser);
  }

  const output = transform(
    input,
    {
      jscodeshift,
      stats: () => {}
    },
    {}
  );
  expect((output || '').trim()).to.equal(expectedOutput.trim());
}

defineTest(__dirname, 'actions-to-typesafe');
