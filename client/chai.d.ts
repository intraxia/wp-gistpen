/* eslint-disable @typescript-eslint/no-unused-vars */
import { Emit, EmitFromDelta } from 'brookjs-desalinate';

declare global {
  namespace Chai {
    interface InstanceOfObservable {
      (): Assertion;
    }

    interface Assertion {
      emit: Emit<Assertion>;
      emitFromDelta: EmitFromDelta<Assertion>;
    }

    interface TypeComparison {
      observable: InstanceOfObservable;
    }
  }
}
