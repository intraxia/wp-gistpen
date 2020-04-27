import React from 'react';
import { render } from '@testing-library/react';
import { Block } from '../Block';
import * as config from '../config';

jest.mock('../Block', () => ({
  Block: (props: React.ComponentProps<typeof Block>) => (
    <div className={props.className}>
      blobId: {props.blobId}; repoId: {props.repoId}
    </div>
  ),
}));

const Edit = config.edit!;
const Save = config.save!;

describe('block', () => {
  it('should render edit from block props', () => {
    const props = {
      className: 'edit',
      isSelected: false,
      attributes: {
        blobId: 123,
        repoId: 456,
      },
      setAttributes: jest.fn(),
    };

    const { container } = render(<Edit {...props} />);

    expect(container).toMatchInlineSnapshot(`
          <div>
            <div
              class="edit"
            >
              blobId: 
              123
              ; repoId: 
              456
            </div>
          </div>
      `);
  });

  it('should render nothing when blobId missing', () => {
    const props = {
      className: 'edit',
      isSelected: false,
      attributes: {
        blobId: null,
        repoId: null,
      },
      setAttributes: jest.fn(),
    };

    const { container } = render(<Save {...props} />);

    expect(container).toMatchInlineSnapshot(`<div />`);
  });

  it('should render shortcode when blobId present', () => {
    const props = {
      className: 'edit',
      isSelected: false,
      attributes: {
        blobId: 123,
        repoId: 456,
      },
      setAttributes: jest.fn(),
    };

    const { container } = render(<Save {...props} />);

    expect(container).toMatchInlineSnapshot(`
      <div>
        [gistpen id="
        123
        "]
      </div>
    `);
  });
});
