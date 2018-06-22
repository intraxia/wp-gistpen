// @flow
import type { Loopable, Cursor, Toggle } from '../../../types';

export type Props = {
    code: string,
    filename: string,
    cursor: Cursor,
    languages: Loopable<string, string>,
    language: string,
    theme: string,
    invisibles: Toggle
};
