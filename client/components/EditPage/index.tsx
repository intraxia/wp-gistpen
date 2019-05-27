import './index.scss';
import React from 'react';
import Controls from './Controls';
import Description from './Description';
import Editor from '../Editor';
import { Toggle, Cursor } from '../../util';

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
  theme: string;
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
  theme
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
            theme={theme}
            {...instance}
            preplug={instance$ =>
              instance$.map(action => ({
                ...action,
                meta: { key: instance.ID }
              }))
            }
          />
        </div>
      ))}
    </div>
  );
};

export default EditPage;
