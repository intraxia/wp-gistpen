import { h, Collector } from 'brookjs-silt';
import injectSheet from 'react-jss';
import classNames from 'classnames';
import jss from 'jss';
import nested from 'jss-nested';
import { commitClick } from '../actions';
import { i18n, link } from '../helpers';
import Blob from './Blob';

jss.use(nested());

const styles = {
    container: {},
    header: {
        'margin-bottom': '10px',
    },
    app: {
        'display': 'flex',
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
        'padding': '2px 10px',
        'margin': '10px',
        'box-shadow': '0 1px 1px 0 rgba(0, 0, 0, 0.1)',
        'cursor': 'pointer',
        'display': 'flex',
        'flex-direction': 'row',
        'align-items': 'center',

        '& p': {
            'margin': '0 2px'
        },

        '& img': {
            'flex-basis': '48px',
            'margin-right': '5px',
            'padding': '2px 0'
        }
    },
    selected: {
        'box-shadow': 'inset 0 1px 1px 0 rgba(0, 0, 0, 0.5)'
    }
};

const Commits = ({ classes, stream$ }) => (
    <Collector silt-embeddable>
        <div className={classes.container}>
            <div className={classes.header}>
                <a href={link('wpgp_route', 'editor')} type="button">
                    {i18n('editor.return')}
                </a>
            </div>
            <div className={classes.app}>
                <div className={classes.list}>
                    {stream$.map(({ commits }) => commits.map(commit => (
                        <div className={stream$.map(({ selectedCommit }) => classNames({
                            [classes.item]: true,
                            [classes.selected]: selectedCommit.ID === commit.ID
                        }))}
                        onClick={evt$ => evt$.map(() => commitClick(commit.ID))}>
                            <img src={commit.author.avatar} alt={commit.author.name} />
                            <p><strong>{commit.description}</strong></p>
                            <p>{commit.committed_at}</p>
                        </div>
                    )))}
                </div>
                <div className={classes.preview}>
                    <h3>{stream$.map(({ selectedCommit }) => selectedCommit.description)}</h3>
                    {stream$.map(({ selectedCommit }) => selectedCommit.states.order.map(id => (
                        <Blob key={id} stream$={stream$.map(({ selectedCommit, prism }) => ({ blob: selectedCommit.states.dict[id], prism }))} />
                    )))}
                </div>
            </div>
        </div>
    </Collector>
);

export default injectSheet(styles)(Commits);
