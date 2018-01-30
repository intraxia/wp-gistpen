// @flow

export type Author = {
    id: number;
    name: string;
    url: string;
    description: string;
    link: string;
    slug: string;
    avatar_urls: {
        [key: string]: string
    }
};
