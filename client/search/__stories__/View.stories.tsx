import React from 'react';
import { View } from '../View';
import { defaultGlobals, defaultPrism } from '../../reducers';
import { searchApiResponse } from '../../mocks';

export default {
  title: 'Search View',
};

export const initial = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="initial"
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const searching = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="searching"
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const foundNoSnippets = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="found"
      snippets={[]}
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const foundSnippets = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="found"
      snippets={searchApiResponse}
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const error = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="error"
      error="Search failed."
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const researching = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="researching"
      snippets={searchApiResponse}
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);

export const reerror = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      term="javascript"
      status="reerror"
      error="Search failed."
      snippets={searchApiResponse}
      globals={defaultGlobals}
      prism={defaultPrism}
    />
  </div>
);
