import type { Observable } from 'kefir';
import { h, view, toJunction } from 'brookjs-silt';
import { i18n, link } from '../../helpers';
import { jobDispatchClick } from '../../actions';
import type { Job, ObservableProps } from '../../types';

const Row = ({ stream$, onClick }: ObservableProps<Job>) => (
    <tr>
        <td><strong>{stream$.thru(view((props: Job) => props.name))}</strong></td>
        <td>{stream$.thru(view(props => props.description))}</td>
        <td>
            {stream$.thru(view(props => props.status))
                .map(status => status || i18n('runs.loading'))}
        </td>
        <td>
            {stream$.thru(view(props => props.slug))
                .map(slug => <a href={link('wpgp_route', `jobs/${slug}`)}>{i18n('jobs.runs.view')}</a>)}
        </td>
        <td>
            {stream$.thru(view(props => props.ID)).map(ID => (
                <button className="button button-primary" onClick={() => onClick(ID)}>
                    {i18n('jobs.dispatch')}
                </button>
            ))}
        </td>
    </tr>
);

export default toJunction({
    events: {
        onClick: (evt$: Observable<Event>) =>
            // @todo this should not be added to the action here
            evt$.map(ID => ({
                ...jobDispatchClick(),
                meta: { key: ID }
            }))
                .debounce(200)
    }
})(Row);
