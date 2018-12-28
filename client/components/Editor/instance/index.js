// @flow
// @jsx h
import './index.scss';
import type { ObservableProps } from '../../../types';
import type { Props } from './types';
import { toJunction, h, view, loop } from 'brookjs-silt';
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

const Filename = ({ stream$, onInput }: ObservableProps<Props>) => (
    <ToolbarButton>
        <span contentEditable="true" spellCheck="false"
            dangerouslySetInnerHTML={stream$.take(1)
                .map(props => ({ __html: props.filename }))}
            onInput={onInput}>
        </span>
    </ToolbarButton>
);

const Language = ({ stream$, onChange }: ObservableProps<Props>) => (
    <ToolbarButton>
        <select onChange={onChange}
            value={stream$.thru(view(props => props.language))}>
            {stream$.thru(loop(props => props.languages, (language$, key) => (
                <option value={key} key={key}>
                    {language$}
                </option>
            )))}
        </select>
    </ToolbarButton>
);

const Delete = ({ onClick }) => (
    <ToolbarButton>
        <button type="button" onClick={onClick}>
            {i18n('editor.delete')}
        </button>
    </ToolbarButton>
);

const Toolbar = ({ children }) => (
    <div className="toolbar">
        {children}
    </div>
);

const Instance = ({ stream$, onFilenameChange, onLanguageChange, onDeleteClick }: ObservableProps<Props>) => (
    <div className="editor page">
        <div className="code-toolbar">
            <Toolbar>
                <Filename stream$={stream$} onInput={onFilenameChange} />
                <Language stream$={stream$} onChange={onLanguageChange} />
                <Delete onClick={onDeleteClick} />
            </Toolbar>
            <Pre stream$={stream$}>
                <Code stream$={stream$} />
            </Pre>
        </div>
    </div>
);

export default toJunction({
    events: {
        onFilenameChange: R.map(R.pipe(R.path(['target', 'textContent']), editorFilenameChangeAction)),
        onLanguageChange: R.map(R.pipe(R.path(['target', 'value']), editorLanguageChangeAction)),
        onDeleteClick: R.map(editorDeleteClickAction)
    }
})(Instance);
