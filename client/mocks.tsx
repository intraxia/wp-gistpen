import {
  SearchBlob,
  SearchBlobsApiResponse,
  SearchRepo,
  SearchReposApiResponse,
} from './search';

export const blob = {
  filename: 'test.js',
  language: 'javascript',
  code: `function $initHighlight(block, flags) {
    try {
        if (block.className.search(/\\bno\\-highlight\\b/) != -1)
            return processBlock(block.function, true, 0x0F) + ' class=""';
    } catch (e) {
        /* handle exception */
        var e4x =
            <div>Example
                <p>1234</p></div>;
    }
    for (var i = 0 / 2; i < classes.length; i++) { // "0 / 2" should not be parsed as regexp
        if (checkCondition(classes[i]) === undefined)
            return /\\d+[\\s/]/g;
    }
    console.log(Array.every(classes, Boolean));
}`,
};

export const prism = {
  theme: 'default',
  'line-numbers': false,
  'show-invisibles': false,
};

export const props = { blob, prism };

let count = 0;

export const createSearchBlob = (): SearchBlob => ({
  ID: ++count,
  filename: blob.filename,
  code: blob.code,
  language: {
    ID: ++count,
    display_name: 'JavaScript',
    slug: blob.language,
  },
  repo_id: ++count,
  rest_url: '',
  repo_rest_url: '',
});

export const searchBlobsApiResponse: SearchBlobsApiResponse = [
  createSearchBlob(),
  createSearchBlob(),
  createSearchBlob(),
  createSearchBlob(),
  createSearchBlob(),
];

export const createSearchRepo = (): SearchRepo => ({
  ID: ++count,
  description: 'Test repo',
  slug: 'test-repo',
  status: 'draft',
  password: '',
  gist_id: null,
  gist_url: '',
  sync: 'off',
  blobs: [],
  rest_url: '',
  commits_url: '',
  html_url: '',
  updated_at: '',
  created_at: '',
});

export const searchReposApiResponse: SearchReposApiResponse = [
  createSearchRepo(),
  createSearchRepo(),
  createSearchRepo(),
  createSearchRepo(),
  createSearchRepo(),
];
