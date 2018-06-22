// @flow
// @jsx h
import './index.scss';
import type { ObservableProps } from '../../../types';
import type { Props } from './types';
import { Collector, h, view, loop } from 'brookjs-silt';
import toolbarStyles from 'prismjs/plugins/toolbar/prism-toolbar.css';
import R from 'ramda';
import { editorFilenameChangeAction, editorDeleteClickAction,
    editorLanguageChangeAction } from '../../../actions';
import { i18n } from '../../../helpers';
import Pre from './Pre';
import Code from './Code';

toolbarStyles.use();

const ToolbarButton = ({ children }) => (
    <div className="toolbar-item">
        {children}
    </div>
);

const Filename = ({ stream$ }: ObservableProps<Props>) => (
    <Collector>
        <ToolbarButton silt-emittable>
            <span contentEditable="true" spellCheck="false"
                dangerouslySetInnerHTML={stream$.take(1)
                    .map(props => ({ __html: props.filename }))}
                onInput={R.map(R.pipe(R.path(['target', 'textContent']), editorFilenameChangeAction))}>
            </span>
        </ToolbarButton>
    </Collector>
);

const Language = ({ stream$ }: ObservableProps<Props>) => (
    <Collector>
        <ToolbarButton silt-emittable>
            <select onChange={evt$ => evt$.map(R.pipe(R.path(['target', 'value']), editorLanguageChangeAction))}
                value={stream$.thru(view(props => props.language))}>
                {stream$.thru(loop(props => props.languages, (language$, key) => (
                    <option value={key} key={key}>
                        {language$}
                    </option>
                )))}
            </select>
        </ToolbarButton>
    </Collector>
);

const Delete = () => (
    <Collector>
        <ToolbarButton silt-emittable>
            <button type="button" onClick={R.map(editorDeleteClickAction)}>
                {i18n('editor.delete')}
            </button>
        </ToolbarButton>
    </Collector>
);

const Toolbar = ({ children }) => (
    <div className="toolbar">
        {children}
    </div>
);

const Instance = ({ stream$ }: ObservableProps<Props>) => (
    <Collector>
        <div className="editor page">
            <div className="code-toolbar">
                <Toolbar>
                    <Filename stream$={stream$} />
                    <Language stream$={stream$} />
                    <Delete />
                </Toolbar>
                <Pre stream$={stream$}>
                    <Code stream$={stream$} />
                </Pre>
            </div>
        </div>
    </Collector>
);

export default Instance;
