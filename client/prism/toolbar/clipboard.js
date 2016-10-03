import Clipboard from 'clipboard';

/**
 * Create a clipboard button for the provided environment.
 *
 * @param {Object} env - Prism environment.
 * @returns {Element} Edit button element.
 */
export default function clipboardButton(env) {
    const linkCopy = document.createElement('a');
    linkCopy.innerHTML = 'Copy';

    const clip = new Clipboard(linkCopy, {
        'text': ()  => env.code
    });

    clip.on('success', () => {
        linkCopy.innerHTML = 'Copied!';

        resetText();
    });
    clip.on('error', () => {
        linkCopy.innerHTML = 'Press Ctrl+C to copy';

        resetText();
    });

    return linkCopy;

    function resetText() {
        setTimeout(() => {
            linkCopy.innerHTML = 'Copy';
        }, 5000);
    }
};
