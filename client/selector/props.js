// @flow
import type { Observable } from 'kefir';
import type { SettingsState, SettingsProps } from '../type';

export function selectSettingsProps(state$ : Observable<SettingsState>) : Observable<SettingsProps> {
    return state$.map((state : SettingsState) => state);
}
