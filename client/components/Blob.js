// @flow
// @jsx h
import type { Subscription } from 'kefir';
import type { Node } from 'react';
import type { Toggle } from '../types';
import { Kefir, raf$ } from 'brookjs';
import { Component } from 'react';
import { h } from 'brookjs-silt';
import Prism from '../prism';

type ObservableProps<T> = {
    stream$: Kefir.Observable<T>
};

type BlobProps = {
    blob: {
        code: string;
        filename: string;
        language: string
    };
    prism: {
        theme: string;
        'line-numbers': number;
        'show-invisibles': Toggle
    }
};

export default class Blob extends Component<ObservableProps<BlobProps>> {
    code: ?Element;
    sub: ?Subscription;

    componentWillMount() {
        Prism.setAutoloaderPath(__webpack_public_path__);
        this.componentDidUpdate();
    }

    componentDidUpdate() {
        this.sub = this.props.stream$.flatMap((props): Kefir.Observable<void> => {
            const promise = Prism.setTheme(props.prism.theme).then((): Promise<Array<void>> => Promise.all([
                Prism.togglePlugin('line-numbers', props.prism['line-numbers']),
                Prism.togglePlugin('show-invisibles', props.prism['show-invisibles'])
            ]));

            return Kefir.fromPromise(promise).flatMap(() => raf$.take(1).flatMap(() => Kefir.stream(emitter => {
                const highlight = () => {
                    if (this.code) {
                        Prism.highlightElement(this.code, false);
                        emitter.end();
                    } else {
                        setTimeout(highlight, 16);
                    }
                };

                highlight();
            })));
        }).observe();
    }

    componentWillUnmount() {
        this.sub && this.sub.unsubscribe();
    }

    render(): Node | Kefir.Observable<Node> {
        const { stream$ } = this.props;

        return (
            <pre className="gistpen line-numbers"
                data-filename={stream$.map(props => props.blob.filename)}>
                <code className={stream$.map(props => `language-${props.blob.language}`)}
                    ref={code => this.code = code}>
                    {stream$.map(props => props.blob.code)}
                </code>
            </pre>
        );
    }
}
