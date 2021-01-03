import * as t from 'io-ts';
import { Toggle } from '../api';

export const ApiMe = t.type({
  editor: t.type({
    indent_width: t.string,
    invisibles_enabled: Toggle,
    tabs_enabled: Toggle,
    theme: t.string,
  }),
});

export type ApiMe = t.TypeOf<typeof ApiMe>;
