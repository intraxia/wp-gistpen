import React from 'react';
import { findDOMNode, render, unmountComponentAtNode } from 'react-dom';
import { Checkbox, Dropdown } from '../wordpress';

const Highlighting = React.createClass({
    statics: {
        rendering: false
    },

    shouldComponentUpdate: function(nextProps) {
        return this.props.prism !== nextProps.prism;
    },

    render: function() {
        return (
            <div className="table">
                <h3 className="title">Syntax Highlighting Settings</h3>
                <table className="form-table">
                    <tbody>
                        <Dropdown
                            label="Choose Theme"
                            selected={this.props.site.prism.theme}
                            options={this.props.themes}
                            onChange={this.props.handlePrismThemeChange} />
                        <Checkbox
                            label="Enable Line Numbers"
                            checked={this.props.site.prism['line-numbers']}
                            onChange={this.props.handleLineNumbersChange} />
                        <Checkbox
                            label="Enable Show Invisibles"
                            checked={this.props.site.prism['show-invisibles']}
                            onChange={this.props.handleShowInvisiblesChange} />
                    </tbody>
                </table>
            </div>
        );
    },

    componentDidMount: function() {
        this.injectIframe();
    },

    componentDidUpdate: function() {
        this.injectIframe();
    },

    injectIframe: function() {
        if (Highlighting.rendering) {
            return setTimeout(this.injectIframe, 0);
        }

        Highlighting.rendering = true;

        const node = findDOMNode(this);
        const swapIframe = () => {
            // Check if the promise has been created.
            if (win.PrismPromise) {
                // If so, use it to swap out the iframe
                // when highlighting is complete.
                win.PrismPromise
                    .then(() => {
                        // After highlighting is complete,
                        // we wait for the next paint to
                        // swap out the iframe.
                        requestAnimationFrame(() => {
                            if (oldIframe) {
                                // Gotta clean up our old React instance
                                // because memory leak.
                                unmountComponentAtNode(
                                    oldIframe
                                        .contentWindow
                                        .document
                                        .querySelector('div')
                                );
                                node.removeChild(oldIframe);
                            }

                            // The big reveal!
                            newIframe.classList.remove('hidden');
                            Highlighting.rendering = false;
                        });
                    });
            } else {
                // Otherwise, requeue and try again.
                setTimeout(swapIframe, 0);
            }
        };

        const oldIframe = node.querySelector('.gistpen');
        const newIframe = document.createElement('iframe');

        newIframe.classList.add('gistpen', 'hidden');
        node.appendChild(newIframe);

        const doc = newIframe.contentWindow.document;
        const win = newIframe.contentWindow;

        const container = document.createElement('div');
        container.classList.add('demo');
        render(this.renderDemo(this.props.site), container);
        doc.body.appendChild(container);

        const min = "1" === this.props.debug ? '' : '.min';
        const web = document.createElement('script');
        web.src = this.props.url + 'assets/js/web' + min + '.js';
        web.async = true;
        web.onload = swapIframe;

        newIframe.contentWindow.Gistpen_Settings = this.props;
        doc.body.appendChild(web);
    },

    renderDemo: function(site) {
        const code = `# Simple for loop using a range.
for i in (1..4)
    print i," "
end
print "\\n"

for i in (1...4)
    print i," "
end
print "\\n"

# Running through a list (which is what they do).
items = [ 'Mark', 12, 'goobers', 18.45 ]
for it in items
    print it, " "
end
print "\\n"

# Go through the legal subscript values of an array.
for i in (0...items.length)
    print items[0..i].join(" "), "\\n"
end`;

        const className = `gistpen ${site.prism['line-numbers'] ? 'line-numbers' : ''}`;

        return (
            <pre className={className}><code className="language-ruby">{code}</code></pre>
        );
    }
});

export default Highlighting;
