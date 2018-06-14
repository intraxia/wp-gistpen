// @flow
// @jsx h
import type { ObservableProps } from '../types';
import { Kefir } from 'brookjs';
import { h, view, withRef$ } from 'brookjs-silt';
import classNames from 'classnames';
import Prism from '../prism';

type PrismProps = {
    theme: string;
    'line-numbers': boolean;
    'show-invisibles': boolean
};

type Props = {
    blob: {
        code: string;
        filename: string;
        language: string
    };
    prism: PrismProps
};

const updatePrism = (prism: PrismProps) => Promise.all([
    Prism.setAutoloaderPath(__webpack_public_path__),
    Prism.setTheme(prism.theme),
    Prism.togglePlugin('line-numbers', prism['line-numbers']),
    Prism.togglePlugin('show-invisibles', prism['show-invisibles'])
]);

const propsToClassName = props => classNames({
    gistpen: true,
    'line-numbers': props.prism['line-numbers']
});

const Code = withRef$(({ stream$ }, ref) => (
    <code ref={ref} className={stream$.thru(view(props => `language-${props.blob.language}`))}>
        {stream$.thru(view(props => props.blob.code))}
    </code>
), (ref$, { stream$ }) => ref$.flatMap(el =>
    stream$.flatMapLatest(props =>
        Kefir.fromPromise(updatePrism(props.prism)).flatMap(() => {
            Prism.highlightElement(el, false);
            return Kefir.never();
        })
    )));

const Blob = ({ stream$ }: ObservableProps<Props>) => (
    <pre className={stream$.thru(view(propsToClassName))}
        data-filename={stream$.thru(view(props => props.blob.filename))}>
        <Code stream$={stream$} />
    </pre>
);

export default Blob;
