// @flow
export * from './accounts';
export * from './ajax';
export * from './author';
export * from './editor';
export * from './highlighting';
export * from './jobs';
export * from './commits';
export * from './router';
export * from './search';
export * from './tinymce';

export const INIT = 'INIT';

export type InitAction<I> = {
    type: typeof INIT,
    payload: { initial: I }
};

export const init = <I>(initial: I): InitAction<I> => ({
    type: INIT,
    payload: { initial }
});

export type Action<I> = InitAction<I>;
