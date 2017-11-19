// @flow
import './index.scss';
import type { Observable } from 'kefir';
import type { HasPrismState, EditorPageProps } from '../../type';
import { component, children, events, render } from 'brookjs';
import template from './index.hbs';
import { commitClick } from '../../action';
import { BlobComponent } from '../../component';

export default component({
    events: events({
        onItemClick: evt$ => evt$.map(evt =>
            commitClick(evt.decoratedTarget.getAttribute('data-id')))
    }),
    children: children({
        blob: {
            factory: BlobComponent,
            modifyChildProps: (props$ : Observable<EditorPageProps>) : Observable<HasPrismState> =>
                props$.map((props : EditorPageProps) : HasPrismState => ({
                    prism: {
                        theme: props.editor.theme,
                        'show-invisibles': props.editor.invisibles === 'on',
                        'line-numbers': true
                    }
                }))
        }
    }),
    render: render(template)
});
