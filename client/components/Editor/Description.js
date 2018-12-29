// @flow
// @jsx h
import './Description.scss';
import type { ObservableProps } from '../../types';
import R from 'ramda';
import { h, view, toJunction } from 'brookjs-silt';
import { i18n } from '../../helpers';
import { editorDescriptionChangeAction } from '../../actions';
import Loader from '../Loader';

type Props = {
    description: string,
    loading: boolean
};

const Description = ({ stream$, onDescriptionChange }: ObservableProps<Props>) => (
    <div id="titlediv" className="wpgp-editor-header-container">
        <div className="wpgp-editor-header-row">
            <div id="titlewrap" className="wpgp-editor-description-container">
                <label id="title-prompt-text" htmlFor="title"
                    className={stream$.thru(view(props => props.description ? 'screen-reader-text' : null))}>
                    {i18n('editor.description')}
                </label>
                <input type="text" defaultValue={stream$.map(props => props.description).take(1)}
                    onInput={onDescriptionChange}
                    name="description" size="30"
                    id="title" spellCheck="true"
                    autoComplete="off" />
            </div>
            <div className="wpgp-editor-loader-container">
                {stream$.thru(view(props => props.loading)).map((loading: boolean) => (
                    loading ? <Loader text={i18n('editor.saving')} /> : null
                ))}
            </div>
        </div>
    </div>
);

export default toJunction({
    events: {
        onDescriptionChange: R.map(R.pipe(R.path(['target', 'value']), editorDescriptionChangeAction))
    }
})(Description);
