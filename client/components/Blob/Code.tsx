import Kefir from 'kefir';
import React from 'react';
import { withRef$, Refback } from 'brookjs';
import Prism from 'prismjs';
import { prismSlug } from '../../helpers';
import { RootAction } from '../../util';
import { setTheme, togglePlugin } from '../../prism';
import { Props } from './types';

const updatePrism = (prism: Props['prism']) =>
  Promise.all([
    setTheme(prism.theme),
    togglePlugin('line-numbers', prism['line-numbers']),
    togglePlugin('show-invisibles', prism['show-invisibles']),
  ]);

const Code: React.RefForwardingComponent<HTMLElement, Props> = (props, ref) => (
  <code ref={ref} className={`language-${prismSlug(props.blob.language)}`}>
    {props.blob.code}
  </code>
);

const refback: Refback<Props, HTMLElement, RootAction> = (ref$, props$) =>
  ref$.flatMap(el =>
    props$
      .skipDuplicates((a, b) => a.prism === b.prism)
      .flatMapLatest(props =>
        Kefir.fromPromise(updatePrism(props.prism)).flatMap(() => {
          Prism.highlightElement(el, false);
          return Kefir.never();
        }),
      ),
  );

export default withRef$(refback)(Code);
