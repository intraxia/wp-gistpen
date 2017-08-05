// @flow
import { getUrl } from '../selector';

export default function link(param : string, name : string) : string {
    return getUrl(param, { name, parts: {} });
}
