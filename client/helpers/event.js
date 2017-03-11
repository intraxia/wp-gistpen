import { eventAttribute } from 'brookjs';
import Handlebars from 'handlebars/runtime';

export default function event(event, callback) {
    return new Handlebars.SafeString(eventAttribute(event, callback));
}
