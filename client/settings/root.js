import component from 'brookjs/component';
import render from 'brookjs/render';
import children from 'brookjs/children';
import header from './header';
import highlighting from './highlighting';
import accounts from './accounts';
import template from './index.hbs';

export default component({
    render: render(template),
    children: children({
        settingsHeader: header,
        settingsHighlighting: highlighting,
        settingsAccounts: accounts
    })
});
