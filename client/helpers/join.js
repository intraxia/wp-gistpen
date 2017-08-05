// @flow

/**
 * Joins the strings with a glue.
 *
 * @param {Array<any>} args - Join arguments.
 * @returns {string} Joined string.
 */
export default function join(...args : Array<any>) : string {
    args.pop();
    const glue = args.pop();

    return args.join(glue);
}
