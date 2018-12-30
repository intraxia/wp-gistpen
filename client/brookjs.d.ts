declare module 'brookjs' {
  import { Observable } from 'kefir';

  type AC<T extends { type: string }> = (...args: any[]) => T;

  // @todo implement this type
  function observeDelta(...args: any[]): any;

  export function ofType(
    ...types: string[]
  ): <V, E>(obs: Observable<V, E>) => Observable<V, E>;
  export function ofType<R extends { type: string }>(
    type: AC<R>
  ): <V, E>(obs: Observable<V, E>) => Observable<R, E>;
}
