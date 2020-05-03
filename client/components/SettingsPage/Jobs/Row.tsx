import React from 'react';
import { toJunction } from 'brookjs';
import { Observable } from 'kefir';
import { i18n, link } from '../../../helpers';
import { jobDispatchClick } from '../../../actions';
import { Job } from './types';

type Props = Job & { onClick: (slug: string) => void };

const Row: React.FC<Props> = ({ name, description, status, slug, onClick }) => (
  <tr>
    <td>
      <strong>{name}</strong>
    </td>
    <td>{description}</td>
    <td>{status}</td>
    <td>
      <a href={link('wpgp_route', `jobs/${slug}`)}>{i18n('jobs.runs.view')}</a>
    </td>
    <td>
      <button
        className="button button-primary"
        data-testid={`dispatch-job-${slug}`}
        onClick={() => onClick(slug)}
      >
        {i18n('jobs.dispatch')}
      </button>
    </td>
  </tr>
);

const events = {
  onClick: (evt$: Observable<string, never>) =>
    evt$.map(jobDispatchClick).debounce(200),
};

export default toJunction(events)(Row);
