import { storiesOf } from '@storybook/react';
import Kefir from 'kefir';
import { h } from 'brookjs-silt';
import Blob from './Blob';

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

storiesOf('Blob', module)
    .add('default', () => (
        <Blob stream$={Kefir.constant({ blob, prism })}/>
    ))
    .add('with line-numbers', () => (
        <Blob stream$={Kefir.constant({ blob, prism: { ...prism, 'line-numbers': true } })}/>
    ))
    .add('with invisibles', () => (
        <Blob stream$={Kefir.constant({ blob, prism: { ...prism, 'show-invisibles': true } })}/>
    ));
