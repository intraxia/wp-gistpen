// @flow
// @jsx h
import type { Theme, ObservableProps } from '../types';
import { h, view, loop, toJunction } from 'brookjs-silt';
import R from 'ramda';
import { lineNumbersChangeAction, showInvisiblesChangeAction,
    themeChangeAction } from '../actions';
import Repo from './Repo';

type HighlightingProps = {
    themes: {
        order: Array<string>,
        dict: { [key: string]: Theme }
    },
    'line-numbers': boolean,
    'show-invisibles': boolean,
    demo: {
        code: string,
        filename: string,
        language: string
    }
};

const Highlighting = ({ stream$, onThemeChange, onLineNumbersChange, onShowInvisiblesChange }: ObservableProps<HighlightingProps>) => (
    <div className="table">
        <h3 className="title">Syntax Highlighting Settings</h3>
        <table className="form-table">
            <tbody>
                <tr>
                    <th><label htmlFor="wpgp-theme">Choose Theme</label></th>
                    <td><select name="wpgp-theme" id="wpgp-theme" onChange={onThemeChange}
                        defaultValue={stream$.thru(view((props: HighlightingProps) => props.themes.order.find(id => props.themes.dict[id].selected)))}>
                        {stream$.map(props => props.themes).thru(loop((theme$, id) => (
                            <option value={id}>{theme$.thru(view(child => child.name))}</option>
                        )))}
                    </select></td>
                </tr>
                <tr>
                    <th><label htmlFor="wpgp-line-numbers">Enable Line Numbers</label></th>
                    <td>
                        <input type="checkbox" name="wpgp-line-numbers"
                            id="wpgp-line-numbers"
                            onChange={onLineNumbersChange}
                            defaultChecked={stream$.thru(view(props => props['line-numbers']))}/>
                    </td>
                </tr>
                <tr>
                    <th><label htmlFor="wpgp-show-invisibles">Enable Show Invisibles</label></th>
                    <td>
                        <input type="checkbox" name="wpgp-show-invisibles"
                            id="wpgp-show-invisibles"
                            onChange={onShowInvisiblesChange}
                            defaultChecked={stream$.thru(view(props => props['show-invisibles']))}/>
                    </td>
                </tr>
            </tbody>
        </table>
        <Repo stream$={stream$.thru(view(({ demo, ...prism }: HighlightingProps) => ({
            blobs: {
                order: ['1'],
                dict: {
                    '1': {
                        ID: '1',
                        ...demo
                    }
                }
            },
            prism: {
                ...prism,
                theme: prism.themes.order.find(id => prism.themes.dict[id].selected)
            }
        })))}/>
    </div>
);


export default toJunction({
    events: {
        onThemeChange: R.map(R.pipe(R.path(['target', 'value']), themeChangeAction)),
        onLineNumbersChange: R.map(
            R.pipe(R.path(['target', 'checked']), lineNumbersChangeAction)
        ),
        onShowInvisiblesChange: R.map(
            R.pipe(R.path(['target', 'checked']), showInvisiblesChangeAction)
        ),
    }
})(Highlighting);
