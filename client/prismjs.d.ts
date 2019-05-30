declare module 'prismjs/components/prism-core' {
  type RegisterCallback = (env: PrismEnv) => HTMLElement | undefined;

  interface Autoloader {
    languages_path: string;
  }

  interface Toolbar {
    registerButton(name: string, register: RegisterCallback): void;
  }

  interface Plugins {
    autoloader: Autoloader;
    toolbar: Toolbar;
  }

  interface Languages {
    [key: string]: {};
  }

  interface Environment {
    element: HTMLElement;
  }

  interface PrismCore {
    highlightAll(): void;
    highlightElement(
      element: Element,
      async?: boolean,
      callback?: Function
    ): void;
    hooks: {
      add(event: string, callback: (env: Environment) => void): void;
    };
    plugins: Plugins;
    languages: Languages;
  }

  interface PrismEnv {
    element: HTMLElement;
  }

  const Prism: PrismCore;

  export default Prism;
}
