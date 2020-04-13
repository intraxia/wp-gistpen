declare module '*.lazy.css' {
  const css: {
    use(): void;
    unuse(): void;
  };

  export default css;
}

declare module '*.module.css' {
  const css: Record<string, string>;

  export default css;
}

declare module '*.module.scss' {
  const css: Record<string, string>;

  export default css;
}
