import path from 'path';
import { Command } from 'brookjs-cli';
import { ofType } from 'brookjs-flow';
import { useDeltas } from 'brookjs-silt';
import { Delta } from 'brookjs-types';
import { Box, Color, AppContext } from 'ink';
import Kefir, { Observable } from 'kefir';
import React, { useContext, useEffect } from 'react';
import execa from 'execa';
import {
  ActionType,
  createAction,
  createAsyncAction,
  getType,
  ActionCreator
} from 'typesafe-actions';
import jest from 'jest';
import { buildArgv } from 'jest-cli/build/cli';
import fs from './fs';

type Maybe<T> = T | null | undefined;

const unreachable = (x: never): never => {
  throw new Error('unreachable value found ' + x);
};

const isPromise = (obj: any): obj is Promise<any> =>
  !!obj &&
  (typeof obj === 'object' || typeof obj === 'function') &&
  typeof obj.then === 'function';

type State = {
  cwd: string;
  e2e: E2ERC;
  status:
    | 'idle'
    | 'starting'
    | 'started'
    | 'startup-failed'
    | 'running-tests'
    | 'tests-passed'
    | 'tests-failed';
};

const actions = {
  init: createAction('INIT'),
  startup: createAsyncAction(
    'STARTUP_REQUESTED',
    'STARTUP_SUCCEEDED',
    'STARTUP_FAILED'
  )<void, { msg: string }, Error>(),
  testRun: createAsyncAction(
    'TEST_RUN_REQUESTED',
    'TEST_RUN_SUCCEEDED',
    'TEST_RUN_FAILED'
  )<void, void, void>(),
  shutdown: createAsyncAction(
    'SHUTDOWN_REQUESTED',
    'SHUTDOWN_SUCCEEDED',
    'SHUTDOWN_FAILED'
  )<void, void, void>()
};

type Action = ActionType<typeof actions>;

type E2EExec = Maybe<Observable<string, Error> | Promise<string>>;

interface E2ERC {
  dir?: string;
  startup?(): E2EExec;
  shutdown?(): E2EExec;
}

// This is what's going to be defined in the E2E key in the rc file.
const e2e: E2ERC = {
  async startup() {
    await execa.command('wp-scripts env start');
    return 'Started!';
  },

  async shutdown() {
    await execa.command('wp-scripts env stop');
    return 'Stopped!';
  }
};

const toObs = (ret: E2EExec): Observable<string, Error> => {
  // Normalize other values to Observables.
  if (ret == null) {
    return Kefir.constant('');
  }

  if (isPromise(ret)) {
    return Kefir.fromPromise(ret);
  }

  return ret;
};

const startup: Delta<Action, State> = (action$, state$) =>
  Kefir.concat([
    // @TODO(mAAdhaTTah) use `constant` when useDeltas is fixed.
    Kefir.later(0, actions.startup.request()),
    state$.take(1).flatMap(
      (state): Observable<Action, never> => {
        const ret = toObs(state.e2e.startup?.())
          .map(msg => actions.startup.success({ msg }))
          .flatMapErrors(err => Kefir.constant(actions.startup.failure(err)));

        return state.e2e.shutdown == null
          ? ret.takeUntilBy(action$.thru(ofType(actions.shutdown.request)))
          : ret;
      }
    )
  ]);

const setupTestsPath = (state: State, testExtension: string) =>
  path.join(state.cwd, state.e2e.dir ?? 'e2e', `setupTests.${testExtension}`);

const sampleStateAtAction = <A extends { type: string }, S>(
  action$: Observable<A, never>,
  state$: Observable<S, never>,
  action: ActionCreator<A['type']>
) => state$.sampledBy(action$.thru(ofType(action)));

const tests: Delta<Action, State> = (action$, state$) =>
  sampleStateAtAction(action$, state$, actions.startup.success)
    .flatMap(state =>
      Kefir.combine({
        cwd: Kefir.constant(state.cwd),
        dir: Kefir.constant(state.e2e.dir ?? 'e2e'),
        setupTests: fs
          // If tsconfig.json exists, we're going to assume typescript.
          .access(path.join(state.cwd, 'tsconfig.json'))
          .map(() => 'ts')
          .flatMapErrors(() => Kefir.constant('js'))
          .flatMap(testExtension =>
            fs
              // If setupTests.{ts,js} exists in the src dir, then we'll use it.
              .access(setupTestsPath(state, testExtension))
              .map(() => [
                `<rootDir>/${state.e2e.dir ??
                  'e2e'}/setupTests.${testExtension}`
              ])
              .flatMapErrors(() => Kefir.constant([]))
          )
      })
    )
    .map(({ cwd, dir, setupTests }) => {
      const argv = [];

      const config: any = {
        roots: [path.join('<rootDir>', dir)],
        preset: 'jest-puppeteer',
        setupFilesAfterEnv: setupTests,
        testMatch: [
          // Anything with `spec/test` is a test file
          // Don't glob `__tests__` because test utils
          `<rootDir>/${dir}/**/*.{spec,test}.{js,jsx,ts,tsx}`
        ],
        transform: {
          '^.+\\.(js|jsx|ts|tsx)$': 'babel-jest',
          '^.+\\.css$': require.resolve(
            path.join('brookjs-cli', 'jest', 'cssTransform.js')
          ),
          '^(?!.*\\.(js|jsx|ts|tsx|css|json)$)': require.resolve(
            path.join('brookjs-cli', 'jest', 'fileTransform.js')
          )
        },
        transformIgnorePatterns: [
          '[/\\\\]node_modules[/\\\\].+\\.(js|jsx|ts|tsx)$',
          '^.+\\.module\\.(css|sass|scss)$'
        ],
        moduleNameMapper: {
          '^react-native$': 'react-native-web',
          '^.+\\.module\\.(css|sass|scss)$': 'identity-obj-proxy'
        },
        moduleFileExtensions: ['js', 'jsx', 'ts', 'tsx', 'node']
      };

      argv.push(`--config`, JSON.stringify(config));
      argv.push('--runInBand');

      return [argv, [cwd]];
    })
    .flatMap(([argv, projects]) =>
      Kefir.stream(emitter => {
        process.env.NODE_ENV = 'test';
        process.env.BABEL_ENV = 'test';
        emitter.value(actions.testRun.request());
        jest.runCLI(buildArgv(argv), projects).then(({ results }) => {
          if (results.success) {
            emitter.value(actions.testRun.success());
          } else {
            emitter.value(actions.testRun.failure());
          }
          emitter.value(actions.shutdown.request());
        });
      })
    );

const reducer = (state: State, action: Action): State => {
  switch (action.type) {
    case getType(actions.startup.request):
      return {
        ...state,
        status: 'starting'
      };
    case getType(actions.startup.success):
      return {
        ...state,
        status: 'started'
      };
    case getType(actions.startup.failure):
      return {
        ...state,
        status: 'startup-failed'
      };
    case getType(actions.testRun.request):
      return {
        ...state,
        status: 'running-tests'
      };
    case getType(actions.testRun.success):
      return {
        ...state,
        status: 'tests-passed'
      };
    case getType(actions.testRun.failure):
      return {
        ...state,
        status: 'tests-failed'
      };
    default:
      return state;
  }
};

const useExit = (error?: Error) => {
  const { exit } = useContext(AppContext);

  useEffect(() => {
    exit(error);
  }, [exit]);
};

const Fail: React.FC = () => {
  useExit(new Error());

  return <Color red>Tests failed!</Color>;
};

const Status: React.FC<{ status: State['status'] }> = ({ status }) => {
  switch (status) {
    case 'idle':
      return <Color yellow>Booting...</Color>;
    case 'starting':
      return <Color yellow>Starting application...</Color>;
    case 'started':
      return <Color green>Startup successful!</Color>;
    case 'startup-failed':
      return <Color red>Startup failed!</Color>;
    case 'running-tests':
      return <Color yellow>Running tests</Color>;
    case 'tests-passed':
      return <Color green>Tests passed!</Color>;
    case 'tests-failed':
      return <Fail />;
    default:
      return unreachable(status);
  }
};

const E2ECommand: Command<{}> = {
  cmd: 'e2e',
  describe: 'Run the application e2e tests.',
  builder(yargs) {
    return yargs;
  },
  View: ({ cwd }) => {
    const { state } = useDeltas(reducer, { e2e, cwd, status: 'idle' }, [
      startup,
      tests
    ]);

    return (
      <Box>
        <Status status={state.status} />
      </Box>
    );
  }
};

export default E2ECommand;
