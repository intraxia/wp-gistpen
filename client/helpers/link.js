// @flow
import { getUrl } from '../selectors';

export default function link(param : string, name : string) : string {
    return getUrl(param, { name, parts: {} });
}
