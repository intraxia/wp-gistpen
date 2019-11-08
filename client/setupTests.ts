/* eslint-env jest */
import { chaiPlugin } from 'brookjs-desalinate';
import Kefir from 'kefir';
import { use } from 'chai';

const { plugin } = chaiPlugin({ Kefir });
use(plugin);
