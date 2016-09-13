import component from 'brookjs/component';
import render from 'brookjs/render';
import children from 'brookjs/children';
import header from './header';
import template from './index.hbs';

export default component({
    render: render(template),
    children: children({
        settingsHeader: header
    })
});
