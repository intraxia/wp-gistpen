import * as t from 'io-ts';

export const Toggle = t.union([t.literal('on'), t.literal('off')]);

export type Toggle = t.TypeOf<typeof Toggle>;
