export type Props = {
  blob: {
    code: string;
    filename: string;
    language: string;
  };
  prism: {
    theme: string;
    'line-numbers': boolean;
    'show-invisibles': boolean;
  };
};
