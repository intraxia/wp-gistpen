import React, { useEffect, useMemo } from 'react';
import Prism from 'prismjs';
import {
  useDelta,
  EddyReducer,
  Delta,
  Maybe,
  RootJunction,
  sampleByAction,
  combineReducers,
  ofType,
  loop,
} from 'brookjs';
import Kefir, { Observable } from 'kefir';
import { Spinner, PanelBody, Panel } from '@wordpress/components';
import { ajax$ } from 'kefir-ajax';
import { StateType, getType } from 'typesafe-actions';
import { InspectorControls } from '@wordpress/block-editor';
import { Editor, editorStateChange } from '../../editor';
import { RootAction } from '../../RootAction';
import { prismSlug, setTheme, togglePlugin } from '../../prism';
import { fetchBlob, ApiBlob } from '../../snippet';
import {
  useGlobals,
  defaultGlobals,
  globalsReducer,
  globalsChanged,
} from '../../globals';
import { foldResponse, AjaxError } from '../../api';
import {
  editLanguageChange,
  editFilenameChange,
  saveSnippetClick,
  saveEditorClick,
  editThemeChange,
  editTabsChange,
  editWidthChange,
  editShowInvisiblesChange,
  saveSiteClick,
  editLineNumbersChange,
  saveBlob,
  embedChanged,
} from '../actions';
import {
  CheckboxControl,
  TextControl,
  SelectControl,
  Button,
  actions as wpActions,
} from '../../wp';
import languageResource from '../../../resources/languages.json';
import { ApiSettings, fetchSettings, saveSettings } from '../../settings';
import styles from './EditEmbed.module.scss';

const languages = Object.entries(languageResource.list).reduce<
  { value: string; label: string }[]
>((entry, [value, label]) => [...entry, { value, label }], []);

type EmbedState = {
  repoId: number;
  blobId: number;
  error: Maybe<AjaxError>;
  blob: Maybe<ApiBlob>;
  code: string;
  filename: string;
  language: string;
  theme: string;
  width: number;
  tabs: boolean;
  showInvisibles: boolean;
  lineNumbers: boolean;
};

const defaultEmbedState: EmbedState = {
  repoId: -1,
  blobId: -1,
  error: null,
  blob: null,
  filename: '',
  code: '',
  language: 'plaintext',
  theme: 'default',
  width: 2,
  tabs: false,
  showInvisibles: false,
  lineNumbers: false,
};

const embedReducer: EddyReducer<EmbedState, RootAction> = (
  state = defaultEmbedState,
  action: RootAction,
) => {
  switch (action.type) {
    case getType(fetchBlob.success):
      return {
        ...state,
        error: null,
        blob: action.payload,
        filename: action.payload.filename,
        language: action.payload.language.slug,
      };
    case getType(fetchBlob.failure):
      return {
        ...state,
        error: action.payload,
        blob: null,
      };
    case getType(fetchSettings.success):
      return {
        ...state,
        theme: action.payload.prism.theme,
        showInvisibles: action.payload.prism['show-invisibles'],
        lineNumbers: action.payload.prism['line-numbers'],
      };
    case getType(editLanguageChange):
      return {
        ...state,
        language: action.payload.value,
      };
    case getType(editFilenameChange):
      return {
        ...state,
        filename: action.payload.value,
      };
    case getType(editThemeChange):
      return {
        ...state,
        theme: action.payload.value,
      };
    case getType(editWidthChange):
      return {
        ...state,
        width: action.payload.value,
      };
    case getType(editTabsChange):
      return {
        ...state,
        tabs: action.payload.checked,
      };
    case getType(editShowInvisiblesChange):
      return {
        ...state,
        showInvisibles: action.payload.checked,
      };
    case getType(editLineNumbersChange):
      return {
        ...state,
        lineNumbers: action.payload.checked,
      };
    case getType(editorStateChange):
      return {
        ...state,
        code: action.payload.code,
      };
    case getType(saveSnippetClick):
      return loop(state, saveBlob.request());
    case getType(saveSiteClick):
      return loop(
        state,
        saveSettings.request({
          prism: {
            'line-numbers': state.lineNumbers,
            'show-invisibles': state.showInvisibles,
            theme: state.theme,
          },
        }),
      );
    case getType(embedChanged):
      return loop(
        {
          ...state,
          repoId: action.payload.repoId,
          blobId: action.payload.blobId,
        },
        [
          fetchBlob.request({
            repoId: action.payload.repoId,
            blobId: action.payload.blobId,
          }),
          fetchSettings.request(),
        ],
      );
    default:
      return state;
  }
};

const reducer = combineReducers({
  embed: embedReducer,
  globals: globalsReducer,
});

type State = StateType<typeof reducer>[0];

const rootDelta: Delta<RootAction, State> = (action$, state$) => {
  const fetch$ = state$
    .thru(sampleByAction(action$, fetchBlob.request))
    .flatMap(state =>
      ajax$(
        `${state.globals.root}repos/${state.embed.repoId}/blobs/${state.embed.blobId}`,
      ).thru(foldResponse(ApiBlob, fetchBlob.success, fetchBlob.failure)),
    );

  const saveBlob$ = state$
    .thru(sampleByAction(action$, saveBlob.request))
    .flatMap(state =>
      ajax$(
        `${state.globals.root}repos/${state.embed.repoId}/blobs/${state.embed.blobId}`,
        {
          method: 'PUT',
          body: JSON.stringify({
            code: state.embed.code,
            filename: state.embed.filename,
            language: state.embed.language,
          }),
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': state.globals.nonce,
          },
        },
      ).thru(foldResponse(ApiBlob, () => saveBlob.success(), saveBlob.failure)),
    );

  const fetchSettings$ = state$
    .thru(sampleByAction(action$, fetchSettings.request))
    .flatMap(state =>
      ajax$(`${state.globals.root}site`, {
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': state.globals.nonce,
        },
      }).thru(
        foldResponse(ApiSettings, fetchSettings.success, fetchSettings.failure),
      ),
    );

  const saveSettings$ = state$
    .thru(sampleByAction(action$, saveSettings.request))
    .zip(
      action$.thru(ofType(saveSettings.request)),
      (state, action) => [state, action] as const,
    )
    .flatMap(([state, action]) =>
      ajax$(`${state.globals.root}site`, {
        method: 'PATCH',
        body: JSON.stringify(action.payload),
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': state.globals.nonce,
        },
      }).thru(
        foldResponse(ApiSettings, saveSettings.success, saveSettings.failure),
      ),
    );

  return Kefir.merge<RootAction, never>([
    fetch$,
    saveBlob$,
    fetchSettings$,
    saveSettings$,
  ]);
};

const initialState = { embed: defaultEmbedState, globals: defaultGlobals };

const preplugFilename = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.change))
    .map(a => editFilenameChange(a.payload.value));
const preplugLanguages = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.change))
    .map(a => editLanguageChange(a.payload.value));
const preplugSaveSnippet = (e$: Observable<RootAction, never>) =>
  e$.thru(ofType(wpActions.click)).map(() => saveSnippetClick());
const preplugSaveEditor = (e$: Observable<RootAction, never>) =>
  e$.thru(ofType(wpActions.click)).map(() => saveEditorClick());
const preplugSaveSite = (e$: Observable<RootAction, never>) =>
  e$.thru(ofType(wpActions.click)).map(() => saveSiteClick());
const preplugTheme = (e$: Observable<RootAction, never>) =>
  e$.thru(ofType(wpActions.change)).map(a => editThemeChange(a.payload.value));
const preplugWidth = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.change))
    .map(a => editWidthChange(Number(a.payload.value)));
const preplugTabs = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.checked))
    .map(a => editTabsChange(a.payload.isChecked));
const preplugShowInvisibles = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.checked))
    .map(a => editShowInvisiblesChange(a.payload.isChecked));
const preplugLineNumbers = (e$: Observable<RootAction, never>) =>
  e$
    .thru(ofType(wpActions.checked))
    .map(a => editLineNumbersChange(a.payload.isChecked));

const EditEmbed: React.FC<{ blobId: number; repoId: number }> = ({
  blobId,
  repoId,
}) => {
  const globals = useGlobals();
  const { state, dispatch, root$ } = useDelta(reducer, initialState, rootDelta);

  useEffect(() => {
    dispatch(globalsChanged(globals));
  }, [dispatch, globals]);

  useEffect(() => {
    dispatch(embedChanged(repoId, blobId));
  }, [dispatch, repoId, blobId]);

  useEffect(() => {
    setTheme(state.embed.theme);
  }, [state.embed.theme]);

  useEffect(() => {
    togglePlugin('show-invisibles', state.embed.showInvisibles);
  }, [state.embed.showInvisibles]);

  const themes = useMemo(
    () =>
      Object.entries(state.globals.themes).map(([value, label]) => ({
        value,
        label,
      })),
    [state.globals.themes],
  );

  const widths = useMemo(
    () =>
      state.globals.ace_widths.map(width => ({
        value: `${width}`,
        label: `${width}`,
      })),
    [state.globals.ace_widths],
  );

  return (
    <RootJunction root$={root$}>
      <InspectorControls>
        <Panel>
          <PanelBody title="Snippet">
            <TextControl
              label="Filename"
              value={state.embed.filename}
              preplug={preplugFilename}
            />
            <SelectControl
              label="Language"
              options={languages}
              value={state.embed.language}
              preplug={preplugLanguages}
            />
            <Button isPrimary preplug={preplugSaveSnippet}>
              Save Snippet
            </Button>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Editor" initialOpen={false}>
            <SelectControl
              label="Indentation Width"
              options={widths}
              value={`${state.embed.width}`}
              preplug={preplugWidth}
            />
            <CheckboxControl
              label="Tabs Enabled"
              checked={state.embed.tabs}
              preplug={preplugTabs}
            />
            <Button isPrimary preplug={preplugSaveEditor}>
              Save
            </Button>
          </PanelBody>
        </Panel>
        <Panel>
          <PanelBody title="Site" initialOpen={false}>
            <SelectControl
              label="Theme"
              options={themes}
              value={state.embed.theme}
              preplug={preplugTheme}
            />
            <CheckboxControl
              label="Enable Line Numbers"
              checked={state.embed.lineNumbers}
              preplug={preplugLineNumbers}
            />
            <CheckboxControl
              label="Enable Show Invisibles"
              checked={state.embed.showInvisibles}
              preplug={preplugShowInvisibles}
            />
            <Button isPrimary preplug={preplugSaveSite}>
              Save
            </Button>
          </PanelBody>
        </Panel>
      </InspectorControls>
      <div data-testid="edit-embed" className={styles.container}>
        {state.embed.blob == null ? (
          <Spinner />
        ) : (
          <Editor
            className={styles.editor}
            Prism={Prism}
            language={prismSlug(state.embed.language)}
            initialCode={state.embed.blob.code}
            width={state.embed.width}
            tabs={state.embed.tabs}
          />
        )}
      </div>
    </RootJunction>
  );
};

export default EditEmbed;
