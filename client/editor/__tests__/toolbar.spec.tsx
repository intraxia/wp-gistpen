import React from 'react';
import { render, fireEvent } from '@testing-library/react';
import { ToolbarInput, ToolbarSelect } from '../toolbar';

describe('toolbar', () => {
  it('should render & emit input', () => {
    const value = 'filename.js';
    const newValue = 'newfilename.js';
    const onChange = jest.fn();
    const { getByLabelText } = render(
      <ToolbarInput value={value} label="filename" onChange={onChange} />,
    );

    const $filename = getByLabelText('filename');
    expect($filename).toHaveTextContent(value);

    // Monkeypatch b/c contentEditable is not supported in jsdom
    $filename.textContent = newValue;
    fireEvent.input($filename);

    expect(onChange).toHaveBeenCalledTimes(1);
    expect(onChange).toHaveBeenCalledWith(newValue);
  });

  it('should render & emit select', () => {
    const value = 'yes';
    const onChange = jest.fn();
    const { getByLabelText } = render(
      <ToolbarSelect
        name="sync"
        value={value}
        label="sync?"
        options={
          [
            { value: 'yes', label: 'Yes' },
            { value: 'no', label: 'No' },
          ] as const
        }
        onChange={onChange}
      />,
    );

    const $sync = getByLabelText('sync?');

    expect($sync).toHaveValue('yes');

    fireEvent.change($sync, { target: { value: 'no' } });

    expect(onChange).toHaveBeenCalledTimes(1);
    expect(onChange).toHaveBeenCalledWith('no');
  });
});
