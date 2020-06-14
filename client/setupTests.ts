import '@testing-library/jest-dom/extend-expect';
import 'brookjs-desalinate/extend-expect';

beforeEach(() => {
  Object.defineProperty(window, 'matchMedia', {
    writable: true,
    value: jest.fn().mockImplementation(query => ({
      matches: false,
      media: query,
      onchange: null,
      addListener: jest.fn(), // deprecated
      removeListener: jest.fn(), // deprecated
      addEventListener: jest.fn(),
      removeEventListener: jest.fn(),
      dispatchEvent: jest.fn(),
    })),
  });
});

// @TODO(mAAdhaTTah) can we get rid of this?
(global as any).Headers = class Headers {
  forEach(cb: () => void) {
    return;
  }

  get(header: string) {
    return;
  }
};

(global as any).__webpack_public_path__ = '/';
