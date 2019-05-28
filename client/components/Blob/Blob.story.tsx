import { storiesOf } from '@storybook/react';
import React from 'react';
import Blob from './index';

const blob = {
  filename: 'test.js',
  language: 'javascript',
  code: `function $initHighlight(block, flags) {
    try {
        if (block.className.search(/\\bno\\-highlight\\b/) != -1)
            return processBlock(block.function, true, 0x0F) + ' class=""';
    } catch (e) {
        /* handle exception */
        var e4x =
            <div>Example
                <p>1234</p></div>;
    }
    for (var i = 0 / 2; i < classes.length; i++) { // "0 / 2" should not be parsed as regexp
        if (checkCondition(classes[i]) === undefined)
            return /\\d+[\\s/]/g;
    }
    console.log(Array.every(classes, Boolean));
}`
};

const prism = {
  theme: 'default',
  'line-numbers': false,
  'show-invisibles': false
};

const props = { blob, prism };

storiesOf('Blob', module)
  .add('default', () => <Blob {...props} />)
  .add('with line-numbers', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'line-numbers': true }
      }}
    />
  ))
  .add('with invisibles', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'show-invisibles': true }
      }}
    />
  ))
  .add('with inivisibles and line-numbers', () => (
    <Blob
      {...{
        blob,
        prism: { ...prism, 'show-invisibles': true, 'line-numbers': true }
      }}
    />
  ));
