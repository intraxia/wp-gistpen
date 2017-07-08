// @flow
import { children, component, Kefir, render } from 'brookjs';
import { EditorComponent, RevisionsComponent } from '../../component';
import template from './index.hbs';

export const el = (doc : Document) => Kefir.fromCallback((callback : (value : null | HTMLElement) => void) => {
    callback(doc.querySelector('[data-brk-container="edit"]'));
});

export const view = component({
    children: children({
        editor: EditorComponent,
        revisions: RevisionsComponent
    }),
    render: render(template)
});