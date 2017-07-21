import { getUrl } from '../selector';

export default function link(param : string, route : string) {
    return getUrl(param, route);
}
