import { observeDelta } from 'brookjs';
import router from './router';

export default observeDelta(
    router
);
