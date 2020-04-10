import './Description.scss';
import React from 'react';
import { toJunction } from 'brookjs-silt';
import { Observable } from 'kefir';
import { i18n } from '../../helpers';
import { editorDescriptionChange } from '../../actions';
import Loader from '../Loader';

type Props = {
  description: string;
  loading: boolean;
  onDescriptionChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

const Description: React.FC<Props> = ({
  description,
  loading,
  onDescriptionChange
}) => (
  <div id="titlediv" className="wpgp-editor-header-container">
    <div className="wpgp-editor-header-row">
      <div id="titlewrap" className="wpgp-editor-description-container">
        <label
          id="title-prompt-text"
          htmlFor="title"
          className={description ? 'screen-reader-text' : undefined}
        >
          {i18n('editor.description')}
        </label>
        <input
          type="text"
          defaultValue={description}
          onChange={onDescriptionChange}
          name="description"
          size={30}
          id="title"
          spellCheck={true}
          autoComplete="off"
        />
      </div>
      <div className="wpgp-editor-loader-container">
        {loading ? <Loader text={i18n('editor.saving')} /> : null}
      </div>
    </div>
  </div>
);

const events = {
  onDescriptionChange: (
    e$: Observable<React.ChangeEvent<HTMLInputElement>, never>
  ) => e$.map(e => editorDescriptionChange(e.target.value))
};

export default toJunction(events)(Description);
