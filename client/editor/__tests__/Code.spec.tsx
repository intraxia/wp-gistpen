import React from 'react';
import Prism from 'prismjs';
import { RenderResult, fireEvent } from '@testing-library/react';
import Code from '../Code';
import {
  editorCursorMove,
  editorIndent,
  editorMakeNewline,
  editorUndo,
  editorRedo,
  editorMakeComment,
  editorValueChange,
} from '../actions';

const createCode = (rr: RenderResult) => {
  const element = {
    code: () => rr.getByTestId('editor-code'),
  };

  const fire = {
    input: (textContent: string) =>
      fireEvent.input(element.code(), { target: { textContent } }),
    keyDown: () => fireEvent.input(element.code()),
    keyUp: () => fireEvent.input(element.code()),
    blur: () => fireEvent.blur(element.code()),
    focus: () => fireEvent.focus(element.code()),
    tab: (e: Partial<React.KeyboardEvent> = {}) =>
      fireEvent.keyDown(element.code(), {
        keyCode: 9,
        target: element.code(),
        ...e,
      }),
    enter: (e: Partial<React.KeyboardEvent> = {}) =>
      fireEvent.keyDown(element.code(), {
        keyCode: 13,
        target: element.code(),
        ...e,
      }),
    z: (e: Partial<React.KeyboardEvent> = {}) =>
      fireEvent.keyDown(element.code(), {
        keyCode: 90,
        target: element.code(),
        ...e,
      }),
    slash: (e: Partial<React.KeyboardEvent> = {}) =>
      fireEvent.keyDown(element.code(), {
        keyCode: 191,
        target: element.code(),
        ...e,
      }),
  };

  return { element, fire };
};

describe('Code', () => {
  it('should position cursor at the beginning of the code', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], () => {
      // @TODO(mAAdhaTTah) how to verify?
    });
  });

  it('should position cursor at the end of the code', () => {
    expect(
      <Code
        cursor={[23, 23]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], () => {
      // @TODO(mAAdhaTTah) how to verify?
    });
  });

  it('should postion cursor across the line of code', () => {
    expect(
      <Code
        cursor={[0, 23]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], () => {
      // @TODO(mAAdhaTTah) how to verify?
    });
  });

  it('should update the language', () => {
    expect(
      <Code cursor={null} Prism={Prism} code="" language="js" />,
    ).toEmitFromJunction([], rr => {
      const { element } = createCode(rr);
      const $code = element.code();

      expect($code).toHaveClass('language-js');

      rr.rerender(<Code cursor={null} Prism={Prism} code="" language="php" />);

      expect($code).toHaveClass('language-php');
    });
  });

  it('should emit cursor clear on blur', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([[16, KTU.value(editorCursorMove(null))]], rr => {
      const { fire } = createCode(rr);

      fire.blur();
    });
  });

  it('should emit cursor on focus', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([[16, KTU.value(editorCursorMove([0, 0]))]], rr => {
      const { fire } = createCode(rr);

      fire.focus();
    });
  });

  it('should emit indent on tab', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction(
      [
        [
          16,
          KTU.value(
            editorIndent({
              code: "const hello = 'world';",
              cursor: [0, 0],
              inverse: false,
            }),
          ),
        ],
      ],
      (rr, _, clock) => {
        clock.runToFrame();
        const { fire } = createCode(rr);

        fire.tab();
      },
    );
  });

  it('should not emit indent on cmd/ctrl/alt + tab', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.tab({ ctrlKey: true });
      fire.tab({ metaKey: true });
      fire.tab({ altKey: true });
    });
  });

  it('should emit make new line on enter', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction(
      [
        [
          16,
          KTU.value(
            editorMakeNewline({
              code: "const hello = 'world';",
              cursor: [0, 0],
            }),
          ),
        ],
      ],
      (rr, _, clock) => {
        clock.runToFrame();
        const { fire } = createCode(rr);

        fire.enter();
      },
    );
  });

  it('should not emit on z', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.z();
    });
  });

  it('should emit undo on cmd + z', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([[16, KTU.value(editorUndo())]], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.z({ metaKey: true });
    });
  });

  it('should emit undo on ctrl + z', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([[16, KTU.value(editorUndo())]], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.z({ ctrlKey: true });
    });
  });

  it('should emit redo on ctrl + shift + z', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([[16, KTU.value(editorRedo())]], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.z({ ctrlKey: true, shiftKey: true });
    });
  });

  it('should not emit make comment on slash', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.slash();
    });
  });

  it('should emit make comment on cmd + slash', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction(
      [
        [
          16,
          KTU.value(
            editorMakeComment({
              code: "const hello = 'world';",
              cursor: [0, 0],
            }),
          ),
        ],
      ],
      (rr, _, clock) => {
        clock.runToFrame();
        const { fire } = createCode(rr);

        fire.slash({ metaKey: true });
      },
    );
  });

  it('should emit make comment on ctrl + slash', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction(
      [
        [
          16,
          KTU.value(
            editorMakeComment({
              code: "const hello = 'world';",
              cursor: [0, 0],
            }),
          ),
        ],
      ],
      (rr, _, clock) => {
        clock.runToFrame();
        const { fire } = createCode(rr);

        fire.slash({ ctrlKey: true });
      },
    );
  });

  it('should not emit make comment on ctrl + alt + slash', () => {
    expect(
      <Code
        cursor={[0, 0]}
        Prism={Prism}
        code="const hello = 'world';"
        language="js"
      />,
    ).toEmitFromJunction([], (rr, _, clock) => {
      clock.runToFrame();
      const { fire } = createCode(rr);

      fire.slash({ ctrlKey: true, altKey: true });
    });
  });

  it('should emit value on input', () => {
    expect(
      <Code cursor={[0, 0]} Prism={Prism} code="" language="js" />,
    ).toEmitFromJunction(
      [
        [
          16,
          KTU.value(
            editorValueChange({
              code: "const hello = 'world';",
              cursor: [0, 0],
            }),
          ),
        ],
      ],
      (rr, _, clock) => {
        clock.runToFrame();
        const { fire } = createCode(rr);

        fire.input("const hello = 'world';");
      },
    );
  });
});
