import './index.scss';
import { Nullable } from 'typescript-nullable';
import React from 'react';
import { toJunction } from 'brookjs-silt';
import classNames from 'classnames';
import { commitClick } from '../../actions';
import { i18n, link } from '../../helpers';
import Blob from '../Blob';
import { Stream } from 'kefir';

const Commits: React.FC<{
  commits: Array<{
    ID: string;
    author: Nullable<{
      avatar: string;
      name: string;
    }>;
    selected: boolean;
    description: string;
    committedAt: string;
  }>;
  selectedCommit: Nullable<{
    description: string;
    states: Array<{
      ID: string;
      code: string;
      filename: string;
      language: string;
    }>;
  }>;
  prism: {
    theme: string;
    'show-invisibles': boolean;
    'line-numbers': boolean;
  };
  onCommitClick: (id: string) => void;
}> = ({ onCommitClick, commits, selectedCommit, prism }) => (
  <div className="wpgp-commits-container">
    <div className="wpgp-commits-header">
      <a href={link('wpgp_route', 'editor')} type="button">
        {i18n('editor.return')}
      </a>
    </div>
    <div className="wpgp-commits-app">
      <div className="wpgp-commits-list">
        {commits.map(({ author, selected, ID, description, committedAt }) => (
          <div
            key={ID}
            className={classNames({
              'wpgp-commits-item': true,
              'wpgp-commits-selected': selected
            })}
            onClick={() => onCommitClick(ID)}
          >
            {author ? <img src={author.avatar} alt={author.name} /> : null}
            <p>
              <strong>{description}</strong>
            </p>
            <p>{committedAt}</p>
          </div>
        ))}
      </div>
      <div className="wpgp-commits-preview">
        {selectedCommit ? (
          <span>
            <h3>{selectedCommit.description}</h3>
            {selectedCommit.states.map(state => (
              <Blob key={state.ID} prism={prism} blob={state} />
            ))}
          </span>
        ) : null}
      </div>
    </div>
  </div>
);

const events = {
  onCommitClick: (evt$: Stream<string, never>) =>
    evt$.map(key => commitClick(key))
};

export default toJunction(events)(Commits);
