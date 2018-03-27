// @flow
import type { Observable } from 'kefir';
import { h } from 'brookjs-silt';
import { Fragment } from 'react';
import Blob from './Blob';

type RepoProps = {
    blobs: {
        order: Array<string>;
        dict: {
            [key: string]: {
                code: string;
                filename: string;
                language: string
            }
        }
    };
    prism: {
        theme: string;
        'line-numbers': boolean;
        'show-invisibles': boolean
    }
};

export default ({ stream$ }: { stream$: Observable<RepoProps> }) => (
    <Fragment>
        {/* $FlowFixMe */}
        <div>
            {stream$.map((props: RepoProps) => props.blobs.order.map(id => (
                <Fragment>
                    {/* $FlowFixMe */}
                    <Blob key={id} stream$={stream$.map(({ blobs, prism }) => ({
                        prism,
                        blob: blobs.dict[id]
                    }))} />
                </Fragment>
            )))}
        </div>
    </Fragment>
);
