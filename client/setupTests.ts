/* @TODO(mAAdhaTTah) figure out why eslint TS is complaining.
/* eslint-env jest */
/* eslint-disable */
import { jestPlugin } from 'brookjs-desalinate';
import Kefir from 'kefir';

const { extensions, ...obs } = jestPlugin({ Kefir });
expect.extend(extensions);

Object.assign(global, { Kutil: obs });

declare global {
  var Kutil: typeof obs;

  namespace jest {
    interface Matchers<R, T> {
      toEmit(expected: any, callback: any): R;
      toEmitFromDelta(
        expected: any,
        cb?: (a: any, b: any, c: any) => void,
        opts?: {
          timeLimit?: number | undefined;
        }
      ): R;
      toEmitFromJunction(
        expected: any,
        cb?: (a: any, b: any, c: any) => void,
        opt?: {
          timeLimit?: number | undefined;
        }
      ): R;
    }
  }
}
