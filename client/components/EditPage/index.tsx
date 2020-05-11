import './index.scss';
import React, { useRef, useEffect, useMemo } from 'react';
import Editor from '../Editor';
import { Toggle, Cursor } from '../../util';
import { ValidationError } from '../../api';
import Description from './Description';
import Controls from './Controls';

type Instance = {
  ID: string;
  code: string;
  filename: string;
  cursor: Cursor;
  language: string;
};

type Status = {
  slug: string;
  name: string;
};

type Theme = {
  slug: string;
  name: string;
};

type Width = {
  slug: string;
  name: string;
};

type Gist = {
  show: boolean;
  url?: string;
};

type Language = {
  value: string;
  label: string;
};

const ErrorMsg: React.FC<{ error: { message: string; body?: string } }> = ({
  error,
}) => {
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setTimeout(() => {
      requestAnimationFrame(() => {
        ref.current && (ref.current.style.display = 'none');
      });
    }, 7999);
  }, [error]);

  const message = useMemo(() => {
    try {
      // @TODO(mAAdhaTTah) this is tied to the API response BOOOO.
      if (error.body != null) {
        const body = JSON.parse(error.body);

        if (body.message) {
          return <p>{body.message}</p>;
        }
      }
    } catch (e) {
      // do nothing
    }

    return <p>{error.message}</p>;
  }, [error]);

  return (
    <div className="wpgp-editor-error" ref={ref}>
      <h3>An error occurred!</h3>
      {message}
    </div>
  );
};

const Errors: React.FC<{ errors: (ValidationError | TypeError)[] }> = ({
  errors,
}) => (
  <div className="wpgp-editor-errors-container">
    {errors.map((error, i) => (
      <ErrorMsg key={i} error={error} />
    ))}
  </div>
);

const EditPage: React.FC<{
  description: string;
  loading: boolean;
  invisibles: Toggle;
  statuses: Status[];
  themes: Theme[];
  widths: Width[];
  selectedTheme: string;
  selectedStatus: string;
  selectedWidth: string;
  gist: Gist;
  sync: Toggle;
  tabs: Toggle;
  instances: Instance[];
  languages: Language[];
  errors: (ValidationError | TypeError)[];
}> = ({
  description,
  loading,
  invisibles,
  statuses,
  themes,
  widths,
  gist,
  sync,
  tabs,
  selectedTheme,
  selectedStatus,
  selectedWidth,
  instances,
  languages,
  errors,
}) => {
  return (
    <div data-brk-container="editor" className="wpgp-editor">
      <div className="wpgp-editor-row">
        <Description description={description} loading={loading} />
      </div>

      <div className="wpgp-editor-row">
        <Controls
          invisibles={invisibles}
          statuses={statuses}
          themes={themes}
          widths={widths}
          gist={gist}
          sync={sync}
          tabs={tabs}
          selectedTheme={selectedTheme}
          selectedStatus={selectedStatus}
          selectedWidth={selectedWidth}
        />
      </div>

      {instances.map(instance => (
        <div className="wpgp-editor-row" key={instance.ID}>
          <Editor
            invisibles={invisibles}
            languages={languages}
            theme={selectedTheme}
            embedCode={
              !instance.ID.includes('new')
                ? `[gistpen id="${instance.ID}"]`
                : undefined
            }
            {...instance}
            preplug={instance$ =>
              instance$.map(action => ({
                ...action,
                meta: { key: instance.ID },
              }))
            }
          />
        </div>
      ))}
      <Errors errors={errors} />
    </div>
  );
};

export default EditPage;
