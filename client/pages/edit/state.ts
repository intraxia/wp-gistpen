import React from 'react';
import View from './View';
import { combineReducers } from 'brookjs';
import { StateType } from 'typesafe-actions';
import {
  ajaxReducer,
  authorsReducer,
  globalsReducer,
  editorReducer,
  repoReducer,
  commitsReducer,
  routeReducer
} from '../../reducers';

export const reducer = combineReducers({
  ajax: ajaxReducer,
  authors: authorsReducer,
  globals: globalsReducer,
  editor: editorReducer,
  commits: commitsReducer,
  repo: repoReducer,
  route: routeReducer
});

export type State = StateType<typeof reducer>[0];

export const mapStateToProps = (
  state: State
): React.ComponentProps<typeof View> => ({
  route: (state.route && state.route.name) || null,
  edit: {
    description: state.editor.description,
    loading: state.ajax.running,
    invisibles: state.editor.invisibles,
    statuses: Object.keys(state.globals.statuses).map(key => ({
      slug: key,
      name: state.globals.statuses[key]
    })),
    themes: Object.keys(state.globals.themes).map(key => ({
      slug: key,
      name: state.globals.themes[key]
    })),
    widths: state.globals.ace_widths.map(width => ({
      slug: String(width),
      name: String(width)
    })),
    selectedTheme: state.editor.theme,
    selectedStatus: state.editor.status,
    selectedWidth: state.editor.width,
    gist: {
      show: !!state.editor.gist_id,
      url: state.editor.gist_id ? `${state.editor.gist_id}` : undefined
    },
    sync: state.editor.sync,
    tabs: state.editor.tabs,
    instances: state.editor.instances.map(instance => ({
      ID: instance.key,
      code: instance.code,
      filename: instance.filename,
      cursor: instance.cursor,
      language: instance.language
    })),
    languages: Object.keys(state.globals.languages).map(key => ({
      value: key,
      label: state.globals.languages[key]
    })),
    theme: state.editor.theme
  },
  commits: {
    prism: {
      theme: state.editor.theme,
      'show-invisibles': state.editor.invisibles === 'on',
      'line-numbers': true
    },
    commits: state.commits.instances.map(instance => ({
      ID: String(instance.ID),
      selected: instance.ID === state.commits.selected,
      description: instance.description,
      committedAt: instance.committed_at,
      author: (() => {
        const author = state.authors.items[instance.author];

        if (author == null) {
          return null;
        }

        return {
          avatar: author.avatar_urls['48'],
          name: author.name
        };
      })()
    })),
    selectedCommit: (() => {
      const commit = state.commits.instances.find(
        instance => instance.ID === state.commits.selected
      );
      if (commit == null) {
        return null;
      }

      return {
        ID: String(commit.ID),
        description: commit.description,
        states: commit.states.map(state => ({
          ID: String(state.ID),
          code: state.code,
          filename: state.filename,
          language: state.language.slug
        }))
      };
    })()
  }
});
