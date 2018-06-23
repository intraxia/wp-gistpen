// @flow

export type Author = {
    id: number,
    name: string,
    url: string,
    description: string,
    link: string,
    slug: string,
    avatar_urls: {
        [key: string]: string
    }
};

// @todo flesh out & dedupe
export type BlobState = {
    language: {
        slug: string
    }
};

export type Commit = {
    ID: number,
    description: string,
    committed_at: string,
    author: string,
    states: Array<BlobState>
};
