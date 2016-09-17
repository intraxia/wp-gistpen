import { observeDelta } from 'brookjs';
import router from './router';
import site from './site';

export default observeDelta(
    router,
    site
);
