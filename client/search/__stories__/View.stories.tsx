import React from 'react';
import { View } from '../View';
import { defaultPrism } from '../../reducers';
import { searchBlobsApiResponse } from '../../mocks';

export default {
  title: 'Search View',
};

const results = searchBlobsApiResponse.map(resp => ({
  id: resp.ID,
  label: resp.filename,
  render: {
    blob: {
      filename: resp.filename,
      code: resp.code,
      language: resp.language.slug,
    },
    prism: defaultPrism,
  },
}));

export const initial = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
    />
  </div>
);

export const searching = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      isLoading
    />
  </div>
);

export const foundNoSnippets = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      results={[]}
    />
  </div>
);

export const foundSnippets = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      results={results}
    />
  </div>
);

export const foundSnippetsDisabled = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      results={results}
      disabled
    />
  </div>
);

export const error = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      error="Search failed."
    />
  </div>
);

export const researching = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      results={results}
      isLoading
    />
  </div>
);

export const reerror = () => (
  <div className="wp-block" style={{ margin: '0 auto' }}>
    <View
      searchLabel="Search for snippet"
      placeholderLabel="placeholder.js"
      term="javascript"
      error="Search failed."
      results={results}
    />
  </div>
);
