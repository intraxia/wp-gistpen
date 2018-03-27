const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const notifier = require('node-notifier');
const flowPath = require('flow-bin');

const flowOut = msg => stdout => {
    console.log(stdout);

    notifier.notify({ title: 'Flow', message: msg });
};

module.exports = new FlowStatusWebpackPlugin({
    binaryPath: flowPath,
    onSuccess: flowOut('Flow passed'),
    onError: flowOut('Flow failed')
});
