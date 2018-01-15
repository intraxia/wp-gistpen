// @flow
import type { Observable } from 'kefir';
import type { Toggle } from '../types';
import { h } from 'brookjs-silt';
import { Fragment } from 'react';
import Blob from './Blob';

type RepoProps = {
    blobs : {
        order : Array<number>;
        dict : {
            [key : number] : {
                code : string;
                filename : string;
                language : string;
            };
        };
    };
    prism : {
        theme : string;
        'line-numbers' : number;
        'show-invisibles' : Toggle;
    };
};

export default ({ stream$ } : { stream$ : Observable<RepoProps>, }) => (
    <Fragment>
        {/* $FlowFixMe */}
        <div>
            {stream$.map(({ blobs }) => blobs.order.map(id => (
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
