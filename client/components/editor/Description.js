// @flow
// @jsx h
import './Description.scss';
import R from 'ramda';
import { h, view, Collector } from 'brookjs-silt';
import { i18n } from '../../helpers';
import { editorOptionsClickAction, editorDescriptionChangeAction } from '../../actions';

type Props = {
    description: string
};

const Description = ({ stream$ }: ObservableProps<Props>) => (
    <Collector>
        <div id="titlediv" className="wpgp-editor-header-container">
            <div className="wpgp-editor-header-row">
                <div id="titlewrap" className="wpgp-editor-description-container">
                    <label id="title-prompt-text" htmlFor="title"
                        className={stream$.thru(view(props => props.description ? 'screen-reader-text' : null))}>
                        {i18n('editor.description')}
                    </label>
                    <input type="text" defaultValue={stream$.map(props => props.description).take(1)}
                        onInput={R.map(R.pipe(R.path(['target', 'value']), editorDescriptionChangeAction))}
                        name="description" size="30"
                        id="title" spellCheck="true"
                        autoComplete="off" />
                </div>
                <div className="wpgp-editor-options-button-container" onClick={R.map(R.always(editorOptionsClickAction()))}>
                    <div tabIndex="0" className="wpgp-editor-options-button dashicons-before dashicons-admin-settings"><br /></div>
                </div>
            </div>
        </div>
    </Collector>
);

export default Description;
