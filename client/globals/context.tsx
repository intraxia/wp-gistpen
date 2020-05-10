import { createContext, useContext } from 'react';
import { GlobalsState, defaultGlobals } from './state';

const GlobalsContext = createContext<GlobalsState>(defaultGlobals);

export const useGlobals = () => useContext(GlobalsContext);
export const GlobalsProvider = GlobalsContext.Provider;
