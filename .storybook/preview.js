import { addDecorator } from '@storybook/react';
import { withJunction } from 'brookjs-desalinate';
import Prism from '../client/prism';

Prism.setAutoloaderPath('/');

addDecorator(withJunction);
