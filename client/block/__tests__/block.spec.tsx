/* eslint-env jest */
import { render, RenderResult } from '@testing-library/react';
import { createRender } from 'react-testing-kit';
import { Block } from '../Block';

const renderBlock = createRender({
  defaultProps: {
    className: '',
    setAttributes: jest.fn(),
    blobId: null,
    repoId: null,
  },
  component: Block,
  render,
  elements: ({ getByTestId }: RenderResult) => ({
    setEmbed: () => getByTestId('set-embed'),
    editEmbed: () => getByTestId('edit-embed'),
  }),
  fire: () => ({}),
  waitFor: () => ({}),
});

describe('Block', () => {
  it('should render setEmbed with no values', () => {
    const { elements } = renderBlock();

    expect(elements.setEmbed()).toBeInTheDocument();
  });

  it('should render setEmbed with only blobId', () => {
    const { elements } = renderBlock({ blobId: 123 });

    expect(elements.setEmbed()).toBeInTheDocument();
  });

  it('should render edit embed with both repoId & blobId', () => {
    const { elements } = renderBlock({ repoId: 123, blobId: 456 });

    expect(elements.editEmbed()).toBeInTheDocument();
  });
});
