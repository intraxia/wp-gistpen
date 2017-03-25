import component from 'brookjs/component';
import render from 'brookjs/render';
import children from 'brookjs/children';
import header from './header';
import highlighting from './highlighting';
import accounts from './accounts';
import template from './index.hbs';

const prismChanged = (prev, next) =>
    prev.prism.theme === next.prism.theme &&
        prev.prism['line-numbers'] === next.prism['line-numbers'] &&
        prev.prism['show-invisibles'] === next.prism['show-invisibles'];

export default component({
    render: render(template),
    children: children({
        settingsHeader: header,
        settingsHighlighting: {
            factory: highlighting,
            modifyChildProps: props$ => props$.skipDuplicates(prismChanged)
        },
        settingsAccounts: accounts
    })
});
