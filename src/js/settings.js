import React from 'react';
import { render } from 'react-dom';
import { Start, reducer, store } from './admin';
import { Actions } from './wordpress';

const { AJAX } = Actions;

store.subscribe((props) => render(
    <Start {...props} />,
    document.getElementById('wpgp-wrap')
));

store.initialize(
    reducer,
    Object.assign({}, window.Gistpen_Settings, {
        ajax: AJAX.IDLE
    })
);
