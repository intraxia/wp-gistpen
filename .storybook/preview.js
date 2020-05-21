import { addDecorator } from '@storybook/react';
import { withJunction } from 'brookjs-desalinate';
import { setAutoloaderPath } from '../client/prism';

setAutoloaderPath('/');

addDecorator(withJunction);
