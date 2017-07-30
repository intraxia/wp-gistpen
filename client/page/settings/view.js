// @flow
import type { Observable } from 'kefir';
import type { SettingsProps, HasPrismState } from '../../type';
import { component, children, render } from 'brookjs';
import header from './header';
import highlighting from './highlighting';
import accounts from './accounts';
import jobs from './jobs';
import template from './index.hbs';

const prismChanged = (prev : SettingsProps, next : SettingsProps) : boolean =>
    prev.prism.theme === next.prism.theme &&
        prev.prism['line-numbers'] === next.prism['line-numbers'] &&
        prev.prism['show-invisibles'] === next.prism['show-invisibles'];

export default component({
    render: render(template),
    children: children({
        settingsHeader: header,
        settingsHighlighting: {
            factory: highlighting,
            modifyChildProps: (props$ : Observable<SettingsProps>) : Observable<HasPrismState> => props$.skipDuplicates(prismChanged)
        },
        settingsAccounts: accounts,
        settingsJobs: jobs
    })
});
