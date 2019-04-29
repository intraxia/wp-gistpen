import React from 'react';
import { toJunction } from 'brookjs-silt';
import { Observable } from 'kefir';
import {
  lineNumbersChange,
  showInvisiblesChange,
  themeChange
} from '../../actions';
import Blob from '../Blob';

type Theme = {
  name: string;
  slug: string;
};

type HighlightingProps = {
  theme: {
    options: Theme[];
    selected: string;
  };
  'line-numbers': boolean;
  'show-invisibles': boolean;
  demo: {
    code: string;
    filename: string;
    language: string;
  };
};

type Props = HighlightingProps & {
  onThemeChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  onLineNumbersChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onShowInvisiblesChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

const Highlighting: React.FC<Props> = ({
  theme,
  ['line-numbers']: lineNumbers,
  ['show-invisibles']: showInvisibles,
  demo,
  onThemeChange,
  onLineNumbersChange,
  onShowInvisiblesChange
}) => (
  <div className="table">
    <h3 className="title">Syntax Highlighting Settings</h3>
    <table className="form-table">
      <tbody>
        <tr>
          <th>
            <label htmlFor="wpgp-theme">Choose Theme</label>
          </th>
          <td>
            <select
              name="wpgp-theme"
              id="wpgp-theme"
              onInput={onThemeChange}
              value={theme.selected}
            >
              {theme.options.map(option => (
                <option value={option.slug}>{option.name}</option>
              ))}
            </select>
          </td>
        </tr>
        <tr>
          <th>
            <label htmlFor="wpgp-line-numbers">Enable Line Numbers</label>
          </th>
          <td>
            <input
              type="checkbox"
              name="wpgp-line-numbers"
              id="wpgp-line-numbers"
              onChange={onLineNumbersChange}
              checked={lineNumbers}
            />
          </td>
        </tr>
        <tr>
          <th>
            <label htmlFor="wpgp-show-invisibles">Enable Show Invisibles</label>
          </th>
          <td>
            <input
              type="checkbox"
              name="wpgp-show-invisibles"
              id="wpgp-show-invisibles"
              onChange={onShowInvisiblesChange}
              checked={showInvisibles}
            />
          </td>
        </tr>
      </tbody>
    </table>
    <Blob
      {...{
        blob: demo,
        prism: {
          'line-numbers': lineNumbers,
          'show-invisibles': showInvisibles,
          theme: theme.selected
        }
      }}
    />
  </div>
);

const events = {
  onThemeChange: (
    e$: Observable<React.ChangeEvent<HTMLSelectElement>, Error>
  ) => e$.map(e => themeChange(e.target.value)),
  onLineNumbersChange: (
    e$: Observable<React.ChangeEvent<HTMLInputElement>, Error>
  ) => e$.map(e => lineNumbersChange(e.target.checked)),
  onShowInvisiblesChange: (
    e$: Observable<React.ChangeEvent<HTMLInputElement>, Error>
  ) => e$.map(e => showInvisiblesChange(e.target.checked))
};

export default toJunction<Props, typeof events>(events)(Highlighting);
