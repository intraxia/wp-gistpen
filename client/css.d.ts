declare module '*.css' {
  const css: {
    use(): void;
    unuse(): void;
  };

  export default css;
}
