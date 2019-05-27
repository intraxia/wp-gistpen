import React from 'react';
import { toJunction } from 'brookjs-silt';
import injectSheet from 'react-jss';
import classNames from 'classnames';
import jss from 'jss';
import nested from 'jss-nested';
import { commitClick } from '../../actions';
import { i18n, link } from '../../helpers';
import Blob from '../Blob';

jss.use(nested());

const styles = {
  container: {},
  header: {
    'margin-bottom': '10px'
  },
  app: {
    display: 'flex',
    'flex-direction': 'row'
  },
  list: {
    'flex-grow': 25
  },
  preview: {
    'flex-grow': 75
  },
  item: {
    'background-color': 'white',
    padding: '2px 10px',
    margin: '10px',
    'box-shadow': '0 1px 1px 0 rgba(0, 0, 0, 0.1)',
    cursor: 'pointer',
    display: 'flex',
    'flex-direction': 'row',
    'align-items': 'center',

    '& p': {
      margin: '0 2px'
    },

    '& img': {
      'flex-basis': '48px',
      'margin-right': '5px',
      padding: '2px 0'
    }
  },
  selected: {
    'box-shadow': 'inset 0 1px 1px 0 rgba(0, 0, 0, 0.5)'
  }
};

type ClassMap = { [key in keyof typeof styles]: string };

const Commits: React.FC<{
  classes: ClassMap;
  commits: Array<{
    ID: string;
    author?: {
      avatar: string;
      name: string;
    };
    selected: boolean;
    description: string;
    committedAt: string;
  }>;
  selectedCommit: {
    description: string;
    states: Array<{
      ID: string;
      code: string;
      filename: string;
      language: string;
    }>;
  };
  prism: {
    theme: string;
    'show-invisibles': boolean;
    'line-numbers': boolean;
  };
  onCommitClick: (id: string) => void;
}> = ({ classes, onCommitClick, commits, selectedCommit, prism }) => (
  <div className={classes.container}>
    <div className={classes.header}>
      <a href={link('wpgp_route', 'editor')} type="button">
        {i18n('editor.return')}
      </a>
    </div>
    <div className={classes.app}>
      <div className={classes.list}>
        {commits.map(({ author, selected, ID, description, committedAt }) => (
          <div
            key={ID}
            className={classNames({
              [classes.item]: true,
              [classes.selected]: selected
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
      <div className={classes.preview}>
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

export default toJunction({
  onCommitClick: evt$ => evt$.map(key => commitClick(key))
})(injectSheet(styles)(Commits));
