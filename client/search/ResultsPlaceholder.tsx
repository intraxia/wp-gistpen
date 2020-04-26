import React from 'react';
import SearchResult from './SearchResult';

export const ResultsPlaceholder: React.FC<{
  label: string;
}> = ({ label }) => {
  return (
    <>
      <SearchResult label={label} disabled={true} />
      <SearchResult label={label} disabled={true} />
      <SearchResult label={label} disabled={true} />
      <SearchResult label={label} disabled={true} />
      <SearchResult label={label} disabled={true} />
    </>
  );
};
