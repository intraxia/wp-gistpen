import { createContext, useContext } from 'react';
import {
  PrismState,
  GlobalsState,
  defaultGlobals,
  defaultPrism,
} from '../reducers';

const GlobalsContext = createContext<GlobalsState>(defaultGlobals);

export const useGlobals = () => useContext(GlobalsContext);

export const GlobalsProvider = GlobalsContext.Provider;

const PrismConfigContext = createContext<PrismState>(defaultPrism);

export const usePrismConfig = () => useContext(PrismConfigContext);

export const PrismConfigProvider = PrismConfigContext.Provider;
