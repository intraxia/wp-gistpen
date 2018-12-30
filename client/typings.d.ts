declare module 'brookjs' {
  import { Observable } from 'kefir';

  type AC<T extends { type: string }> = (...args: any[]) => T;

  export function ofType(
    ...types: string[]
  ): <V, E>(obs: Observable<V, E>) => Observable<V, E>;
  export function ofType<R extends { type: string }>(
    type: AC<R>
  ): <V, E>(obs: Observable<V, E>) => Observable<R, E>;
}

declare module 'kefir' {
  export type ValueOfAnObservable<T extends Observable<any, any>> = T[''];

  export interface Subscription {
    unsubscribe(): void;
    readonly closed: boolean;
  }

  export interface Observer<T, S> {
    value?: (value: T) => void;
    error?: (error: S) => void;
    end?: () => void;
  }

  export class Observable<T, S> {
    '': T; // TypeScript hack to enable value unwrapping for combine/flatMap

    toProperty(getCurrent?: () => T): Property<T, S>;
    // Subscribe / add side effects
    onValue(callback: (value: T) => void): this;
    offValue(callback: (value: T) => void): this;
    onError(callback: (error: S) => void): this;
    offError(callback: (error: S) => void): this;
    onEnd(callback: () => void): this;
    offEnd(callback: () => void): this;
    onAny(callback: (event: Event<T, S>) => void): this;
    offAny(callback: (event: Event<T, S>) => void): this;
    log(name?: string): this;
    spy(name?: string): this;
    offLog(name?: string): this;
    offSpy(name?: string): this;
    flatten<U>(transformer?: (value: T) => U[]): Stream<U, S>;
    toPromise(): Promise<T>;
    toPromise<W extends PromiseLike<T>>(PromiseConstructor: () => W): W;
    toESObservable(): any;
    // This method is designed to replace all other methods for subscribing
    observe(params: Observer<T, S>): Subscription;
    observe(
      onValue?: (value: T) => void,
      onError?: (error: S) => void,
      onEnd?: () => void
    ): Subscription;
    setName(source: Observable<any, any>, selfName: string): this;
    setName(selfName: string): this;

    thru<R>(cb: (obs: Observable<T, S>) => Observable<R, S>): Observable<R, S>;
    // Modify an stream
    map<U>(fn: (value: T) => U): Observable<U, S>;
    filter<U extends T>(fn: (value: T) => value is U): Observable<U, S>;
    filter(predicate?: (value: T) => boolean): Observable<T, S>;
    take(n: number): Observable<T, S>;
    takeWhile(predicate?: (value: T) => boolean): Observable<T, S>;
    last(): Observable<T, S>;
    skip(n: number): Observable<T, S>;
    skipWhile(predicate?: (value: T) => boolean): Observable<T, S>;
    skipDuplicates(comparator?: (a: T, b: T) => boolean): Observable<T, S>;
    diff(fn?: (prev: T, next: T) => T, seed?: T): Observable<T, S>;
    scan<W>(fn: (prev: T | W, next: T) => W): Observable<W, S>;
    scan<W>(fn: (prev: W, next: T) => W, seed: W): Observable<W, S>;
    delay(wait: number): Observable<T, S>;
    throttle(
      wait: number,
      options?: { leading?: boolean; trailing?: boolean }
    ): Observable<T, S>;
    debounce(wait: number, options?: { immediate: boolean }): Observable<T, S>;
    valuesToErrors(): Observable<never, S | T>;
    valuesToErrors<U>(
      handler: (value: T) => { convert: boolean; error: U }
    ): Observable<never, S | U>;
    errorsToValues<U>(
      handler?: (error: S) => { convert: boolean; value: U }
    ): Observable<T | U, never>;
    mapErrors<U>(fn: (error: S) => U): Observable<T, U>;
    filterErrors(predicate?: (error: S) => boolean): Observable<T, S>;
    endOnError(): Observable<T, S>;
    takeErrors(n: number): Observable<T, S>;
    ignoreValues(): Observable<never, S>;
    ignoreErrors(): Observable<T, never>;
    ignoreEnd(): Observable<T, S>;
    beforeEnd<U>(fn: () => U): Observable<T | U, S>;
    slidingWindow(max: number, mix?: number): Observable<T[], S>;
    bufferWhile(predicate: (value: T) => boolean): Observable<T[], S>;
    bufferWithCount(
      count: number,
      options?: { flushOnEnd: boolean }
    ): Observable<T[], S>;
    bufferWithTimeOrCount(
      interval: number,
      count: number,
      options?: { flushOnEnd: boolean }
    ): Observable<T[], S>;
    transduce<U>(transducer: any): Observable<U, S>;
    withHandler<U, V>(
      handler: (emitter: Emitter<U, S>, event: Event<T, S>) => void
    ): Observable<U, S>;
    // Combine streams
    combine<U, V, W>(
      otherObs: Observable<U, V>,
      combinator?: (value: T, ...values: U[]) => W
    ): Observable<W, S | V>;
    zip<U, V, W>(
      otherObs: Observable<U, V>,
      combinator?: (value: T, ...values: U[]) => W
    ): Observable<W, S | V>;
    merge<U, V>(otherObs: Observable<U, V>): Observable<T | U, S | V>;
    concat<U, V>(otherObs: Observable<U, V>): Observable<T | U, S | V>;
    flatMap<U, V>(transform: (value: T) => Observable<U, V>): Observable<U, V>;
    flatMap<X extends T & Property<T, any>>(): Observable<
      ValueOfAnObservable<X>,
      any
    >;
    flatMapLatest<U, V>(fn: (value: T) => Observable<U, V>): Observable<U, V>;
    flatMapLatest<X extends T & Property<T, any>>(): Observable<
      ValueOfAnObservable<X>,
      any
    >;
    flatMapFirst<U, V>(fn: (value: T) => Observable<U, V>): Observable<U, V>;
    flatMapFirst<X extends T & Property<T, any>>(): Observable<
      ValueOfAnObservable<X>,
      any
    >;
    flatMapConcat<U, V>(fn: (value: T) => Observable<U, V>): Observable<U, V>;
    flatMapConcat<X extends T & Property<T, any>>(): Observable<
      ValueOfAnObservable<X>,
      any
    >;
    flatMapConcurLimit<U, V>(
      fn: (value: T) => Observable<U, V>,
      limit: number
    ): Observable<U, V>;
    flatMapErrors<U, V>(
      transform: (error: S) => Observable<U, V>
    ): Observable<U, V>;
    // Combine two streams
    filterBy<U>(otherObs: Observable<boolean, U>): Observable<T, S>;
    sampledBy(otherObs: Observable<any, any>): Observable<T, S>;
    sampledBy<U, W>(
      otherObs: Observable<U, any>,
      combinator: (a: T, b: U) => W
    ): Observable<W, S>;
    skipUntilBy<U, V>(otherObs: Observable<U, V>): Observable<U, V>;
    takeUntilBy<U, V>(otherObs: Observable<U, V>): Observable<U, V>;
    bufferBy<U, V>(
      otherObs: Observable<U, V>,
      options?: { flushOnEnd: boolean }
    ): Observable<T[], S>;
    bufferWhileBy<U>(
      otherObs: Observable<boolean, U>,
      options?: { flushOnEnd?: boolean; flushOnChange?: boolean }
    ): Observable<T[], S>;
    awaiting<U, V>(otherObs: Observable<U, V>): Observable<boolean, S>;
  }

  export class Stream<T, S> extends Observable<T, S> {}

  export class Property<T, S> extends Observable<T, S> {}

  export interface ObservablePool<T, S> extends Observable<T, S> {
    plug(obs: Observable<T, S>): this;
    unplug(obs: Observable<T, S>): this;
  }

  export type Event<V, E> =
    | { type: 'value'; value: V }
    | { type: 'error'; value: E }
    | { type: 'end'; value: void };

  export interface Emitter<V, E> {
    value(value: V): boolean;
    event(event: Event<V, E>): boolean;
    error(e: E): boolean;
    end(): void;

    // Deprecated methods
    emit(value: V): boolean;
    emitEvent(event: Event<V, E>): boolean;
  }

  // Create a stream
  export function never(): Stream<never, never>;
  export function later<T>(wait: number, value: T): Stream<T, never>;
  export function interval<T>(interval: number, value: T): Stream<T, never>;
  export function sequentially<T>(
    interval: number,
    values: T[]
  ): Stream<T, never>;
  export function fromPoll<T>(interval: number, fn: () => T): Stream<T, never>;
  export function withInterval<T, S>(
    interval: number,
    handler: (emitter: Emitter<T, S>) => void
  ): Stream<T, S>;
  export function fromCallback<T>(
    fn: (callback: (value: T) => void) => void
  ): Stream<T, never>;
  export function fromNodeCallback<T, S>(
    fn: (callback: (error: S, result: T) => void) => void
  ): Stream<T, S>;
  export function fromEvents<T, S>(
    target: EventTarget | NodeJS.EventEmitter | { on: Function; off: Function },
    eventName: string,
    transform?: (value: T) => S
  ): Stream<T, S>;
  export function stream<T, S>(
    subscribe: (emitter: Emitter<T, S>) => Function | void
  ): Stream<T, S>;
  export function fromESObservable<T, S>(observable: any): Stream<T, S>;

  // Create a property
  export function constant<T>(value: T): Property<T, never>;
  export function constantError<T>(error: T): Property<never, T>;
  export function fromPromise<T, S>(promise: Promise<T>): Property<T, S>;
  // Combine observables
  export function combine<T, S, U>(
    obss: Observable<T, S>[],
    passiveObss: Observable<T, S>[],
    combinator?: (...values: T[]) => U
  ): Stream<U, S>;
  export function combine<T, S, U>(
    obss: Observable<T, S>[],
    combinator: (...values: T[]) => U
  ): Stream<U, S>;
  export function combine<T extends { [name: string]: Observable<any, any> }>(
    obss: T
  ): Stream<{ [P in keyof T]: ValueOfAnObservable<T[P]> }, any>;
  // export function combine<T extends [Observable<any, any>], P extends keyof T>(obss: T): Stream<[ValueOfAnObservable<T[0]>, ValueOfAnObservable<T[1]>], any>;
  export function combine<
    T extends [
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>
    ]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>,
      ValueOfAnObservable<T[3]>,
      ValueOfAnObservable<T[4]>,
      ValueOfAnObservable<T[5]>,
      ValueOfAnObservable<T[6]>,
      ValueOfAnObservable<T[7]>
    ],
    any
  >;
  export function combine<
    T extends [
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>
    ]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>,
      ValueOfAnObservable<T[3]>,
      ValueOfAnObservable<T[4]>,
      ValueOfAnObservable<T[5]>,
      ValueOfAnObservable<T[6]>
    ],
    any
  >;
  export function combine<
    T extends [
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>
    ]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>,
      ValueOfAnObservable<T[3]>,
      ValueOfAnObservable<T[4]>,
      ValueOfAnObservable<T[5]>
    ],
    any
  >;
  export function combine<
    T extends [
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>
    ]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>,
      ValueOfAnObservable<T[3]>,
      ValueOfAnObservable<T[4]>
    ],
    any
  >;
  export function combine<
    T extends [
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>,
      Observable<any, any>
    ]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>,
      ValueOfAnObservable<T[3]>
    ],
    any
  >;
  export function combine<
    T extends [Observable<any, any>, Observable<any, any>, Observable<any, any>]
  >(
    obss: T
  ): Stream<
    [
      ValueOfAnObservable<T[0]>,
      ValueOfAnObservable<T[1]>,
      ValueOfAnObservable<T[2]>
    ],
    any
  >;
  export function combine<
    T extends [Observable<any, any>, Observable<any, any>]
  >(
    obss: T
  ): Stream<[ValueOfAnObservable<T[0]>, ValueOfAnObservable<T[1]>], any>;
  export function combine<T extends [Observable<any, any>]>(
    obss: T
  ): Stream<[ValueOfAnObservable<T[0]>], any>;
  export function combine<T extends never[]>(obss: T): Stream<never, never>;
  export function zip<T, S, U>(
    obss: Observable<T, S>[],
    passiveObss?: Observable<T, S>[],
    combinator?: (...values: T[]) => U
  ): Observable<U, S>;
  export function merge<T, S>(obss: Observable<T, S>[]): Observable<T, S>;
  export function concat<T, S>(obss: Observable<T, S>[]): Observable<T, S>;
  export function pool<T, S>(): ObservablePool<T, S>;
  export function repeat<T, S>(
    generator: (i: number) => Observable<T, S> | boolean
  ): Observable<T, S>;

  class Kefir {
    static constant: typeof constant;
    static merge: typeof merge;
    static stream: typeof stream;
    static constantError: typeof constantError;
  }

  export default Kefir;
}
