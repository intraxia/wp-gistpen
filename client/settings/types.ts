import * as t from 'io-ts';

export const ApiSettings = t.type({
  gist: t.type({
    token: t.string,
  }),
  prism: t.type({
    'line-numbers': t.boolean,
    'show-invisibles': t.boolean,
    theme: t.string,
  }),
});

export type ApiSettings = t.TypeOf<typeof ApiSettings>;
