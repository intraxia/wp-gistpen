import { createContext, useContext } from 'react';
import { PrismState, defaultPrism } from '../reducers';

const PrismConfigContext = createContext<PrismState>(defaultPrism);

export const usePrismConfig = () => useContext(PrismConfigContext);

export const PrismConfigProvider = PrismConfigContext.Provider;
