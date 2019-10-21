import { Cursor, Toggle } from '../../util';

export type Props = {
  code: string;
  filename: string;
  cursor: Cursor;
  languages: Array<{
    value: string;
    label: string;
  }>;
  language: string;
  theme: string;
  invisibles: Toggle;
  embedCode?: string;
  onFilenameChange: (e: React.ChangeEvent<HTMLSpanElement>) => void;
  onLanguageChange: (e: React.ChangeEvent<HTMLSelectElement>) => void;
  onDeleteClick: (e: React.MouseEvent<HTMLButtonElement>) => void;
};
