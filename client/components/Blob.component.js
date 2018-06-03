// @flow
// @jsx h
import { Kefir } from 'brookjs';
import { h, view } from 'brookjs-silt';
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

const Blob = ({ stream$ }: ObservableProps<Props>) => {
    const code$ = new Kefir.Stream();

    const highlighting$ = code$.flatMap(el =>
        stream$.flatMapLatest(props =>
            Kefir.fromPromise(updatePrism(props.prism)).flatMap(() => {
                Prism.highlightElement(el, false);
                return Kefir.constant(true);
            })
        ))
        .toProperty(() => false);

    return (
        <pre className={stream$.thru(view(propsToClassName))}
            data-highlighting={highlighting$}
            data-filename={stream$.thru(view(props => props.blob.filename))}>
            <code className={stream$.thru(view(props => `language-${props.blob.language}`))}
                ref={code => code$._emitValue(code)}>
                {stream$.thru(view(props => props.blob.code))}
            </code>
        </pre>
    );
};

export default Blob;
