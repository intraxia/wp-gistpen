import { Observable } from 'rx';
import { FuncSubject } from 'rx-react';
import * as actions from './actions';

/**
 * Streams update gist token actions on textbox events.
 */
export const gistTokenStream = FuncSubject.create(
    (event) => actions.updateGistToken(
        event.target.value
    )
);

/**
 * Streams update theme actions on dropdown events.
 */
export const themeStream = FuncSubject.create(
    (event) => actions.updatePrismTheme(
        event.target.value
    )
);

/**
 * Streams toggle line numbers actions on checkbox events.
 */
export const lineNumbersStream = FuncSubject.create(
    (event) => actions.toggleLineNumbers(
        event.target.checked
    )
);

/**
 * Streams toggle show invisibles actions on checkbox events.
 */
export const showInvisiblesStream = FuncSubject.create(
    (event) => actions.toggleShowInvisibles(
        event.target.checked
    )
);

/**
 * Streams combined actions from all event streams.
 */
export const eventStream = Observable.merge(
    gistTokenStream,
    themeStream,
    lineNumbersStream,
    showInvisiblesStream
);
