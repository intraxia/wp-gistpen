/* eslint-env jest */
import { routerDelta } from '../routerDelta';
import { routeChange } from '../../actions';

const createLocation = (search: string) => {
  const location = {} as any;
  const parser = document.createElement('a');
  parser.href = `http://test.dev/editor?${search}`;
  ([
    'href',
    'protocol',
    'host',
    'hostname',
    'origin',
    'port',
    'pathname',
    'search',
    'hash',
  ] as const).forEach(prop => {
    location[prop] = parser[prop];
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
      routerDelta({ router, param: 'wpgp', location, history }),
    ).toEmitFromDelta(
      [[0, KTU.value(routeChange('/start'))]],
      (_send, tick) => {
        tick(10);
      },
    );
  });
});
