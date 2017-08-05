// @flow

import { eventAttribute } from 'brookjs';
import Handlebars from 'handlebars/runtime';

export default function event(event : string, callback : string) : Handlebars.SafeString {
    return new Handlebars.SafeString(eventAttribute(event, callback));
}
