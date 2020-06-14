import { createContext, useContext, useEffect } from 'react';
import { setAutoloaderPath } from '../prism';
import { GlobalsState, defaultGlobals } from './state';

const GlobalsContext = createContext<GlobalsState>(defaultGlobals);

export const useGlobals = () => {
  const globals = useContext(GlobalsContext);

  useEffect(() => {
    setAutoloaderPath(
      (__webpack_public_path__ = globals.url + 'resources/assets/'),
    );
  }, [globals.url]);

  return globals;
};
export const GlobalsProvider = GlobalsContext.Provider;
