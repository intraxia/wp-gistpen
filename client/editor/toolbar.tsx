import React, { useRef, useLayoutEffect } from 'react';

export const ToolbarItem: React.FC = ({ children }) => {
  return <div className="toolbar-item">{children}</div>;
};

export const ToolbarInput: React.FC<{
  value: string;
  label: string;
  className?: string;
  onChange: (value: string) => void;
}> = ({ value, label, className, onChange }) => {
  const ref = useRef<HTMLSpanElement>(null);

  useLayoutEffect(() => {
    if (ref.current != null && ref.current.textContent !== value) {
      ref.current.textContent = value;
    }
  }, [value]);

  return (
    <ToolbarItem>
      <span
        ref={ref}
        contentEditable
        spellCheck={false}
        onInput={e => onChange(e.currentTarget.textContent ?? '')}
        className={className}
        aria-label={label}
        tabIndex={0}
      />
    </ToolbarItem>
  );
};

export const ToolbarSelect = <T extends string>({
  name,
  label,
  value,
  options,
  onChange,
}: {
  name: string;
  label: string;
  value: T;
  options: ReadonlyArray<{
    value: T;
    label: string;
  }>;
  onChange: (value: T) => void;
}): ReturnType<React.FC> => {
  return (
    <ToolbarItem>
      <label htmlFor={name}>{label}</label>
      <select
        id={name}
        onChange={e => onChange(e.target.value as T)}
        value={value}
      >
        {options.map(option => (
          <option value={option.value} key={option.value}>
            {option.label}
          </option>
        ))}
      </select>
    </ToolbarItem>
  );
};
export const Toolbar: React.FC<{
  children: React.ReactNode;
}> = ({ children }) => {
  return <div className="toolbar">{children}</div>;
};
