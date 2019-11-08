/* eslint-env jest */
import { expect, use } from 'chai';
import Kefir from 'kefir';
import { chaiPlugin } from 'brookjs-desalinate';
import { routerDelta } from '../routerDelta';
import { routeChange } from '../../actions';

const { plugin, value } = chaiPlugin({ Kefir }) as any;

use(plugin);

const createLocation = (search: string) => {
  const location = {} as any;
  const parser = document.createElement('a');
  parser.href = `http://test.dev/editor?${search}`;
  [
    'href',
    'protocol',
    'host',
    'hostname',
    'origin',
    'port',
    'pathname',
    'search',
    'hash'
    // @TODO(mAAdhaTTah) should be as const but bug in TS
    // https://github.com/Microsoft/TypeScript/issues/30664
  ] /* as const */
    .forEach(prop => {
      location[prop] = (parser as any)[prop];
    });

  return location as Location;
};

const createHistory = () => {
  const history = {} as any;

  // @TODO(mAAdhaTTah) fill out methods

  return history as History;
};

describe('routerDelta', () => {
  const router = (route: string) => routeChange(route);

  it('should emit the initial route', () => {
    const location = createLocation('wpgp=start');
    const history = createHistory();
    expect(
      routerDelta({ router, param: 'wpgp', location, history })
    ).to.emitFromDelta([[0, value(routeChange('/start'))]], (_send, tick) => {
      tick(10);
    });
  });
});
