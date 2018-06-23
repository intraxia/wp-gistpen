// @flow
// @jsx h
import type { Emitter, Observable } from 'kefir';
import type { ObservableProps } from '../../../types';
import R from 'ramda';
import { Kefir } from 'brookjs';
import { Collector, h, view, withRef$ } from 'brookjs-silt';
import { prismSlug } from '../../../helpers';
import { lineNumberIsEqual } from './util';
import type { Props } from './types';

// const CRLF = /\r?\n|\r/g;

/**
 * Update the highlighted line numbers next to the editor.
 *
 * @returns {Observable} Observable to update the editor line numbers.
 */
function updateLineNumber(/*pre, start, end*/): Observable<void> {
    return Kefir.stream((emitter: Emitter<void, void>) => {
        // let content = pre.textContent;
        // let ss = pre.selectionStart;
        // let se = pre.selectionEnd;
        //
        // // @todo push into store
        // ss && pre.setAttribute('data-ss', ss);
        // se && pre.setAttribute('data-se', se);

        // Update current line highlight
        // let line = (content.slice(0, ss).match(CRLF) || []).length;

        // pre.setAttribute('data-line', line + 1);

        emitter.end();
    })
        .setName('updateLineNumbers$');
}

const Pre = ({ stream$, children }: ObservableProps<Props> & { children: any }) => (
    <Collector>
        <pre className={stream$.thru(view((props: Props) => `language-${prismSlug(props.language)}`))}
            spellCheck="false" >
            {children}
        </pre>
    </Collector>
);

const ref = (ref$, { stream$ }) => ref$.flatMap((/* el */) => {
    /**
     * Create line number render stream.
     *
     * Update the line numbers as soon as they change. This
     * doesn't need to be affected by the typing, as this
     * won't change anything in the DOM that the editor
     * interacts with.
     */
    const lineNumber$ = stream$.skipDuplicates(lineNumberIsEqual)
        .filter(R.path(['instance', 'cursor']))
        .flatMapLatest((/*props : EditorInstanceProps*/): Observable<void> =>
            updateLineNumber(/* el.querySelector('pre'), ...(props.instance.cursor || [])*/))
        .setName('lineNumbers$');

    return lineNumber$;
});

export default withRef$(Pre, ref);
