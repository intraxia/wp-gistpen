declare module 'brookjs-desalinate' {
  import K, { Event, Stream, Property, Observable } from 'kefir';

  interface ChaiPlugin {
    plugin(chai: any, util: any): void;

    stream<V, E extends Error>(): Stream<V, E>;
    prop<V, E extends Error>(): Property<V, E>;

    value<V, E extends Error>(value: V): Event<V, E>;
    error<V, E extends Error>(err: E): Event<V, E>;
    end<V, E extends Error>(): Event<V, E>;

    send<V, E extends Error>(
      obs: Observable<V, E>,
      evts: Event<V, E>[]
    ): Observable<V, E>;
  }

  export type ToDelta = (action: object, state: object) => void;
  export type Tick = (time: number) => void;

  export type Emit<A> = <V, E>(
    expected: Array<Event<V, E>>,
    cb: () => void
  ) => A;
  export type EmitFromDelta<A> = <V, E>(
    expected: Array<[number, Event<V, E>]>,
    cb?: (sendToDelta: ToDelta, tick: Tick) => void
  ) => A;

  export function chaiPlugin({ Kefir }: { Kefir: typeof K }): ChaiPlugin;
}
