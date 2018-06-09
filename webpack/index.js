const path = require('path');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const WebpackNotifierPlugin = require('webpack-notifier');
const FlowStatusWebpackPlugin = require('flow-status-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const notifier = require('node-notifier');
const flowPath = require('flow-bin');

const client = path.join(__dirname, '..', 'client');
const pages = path.join(client, 'pages');

exports.devtool = 'sourcemap';

exports.eslintRule = {
    test: /\.js$/,
    use: ['eslint-loader'],
    include: client,
    enforce: 'pre'
};

exports.babelRule = {
    test: /\.js$/,
    use: ['babel-loader'],
    include: [
        client,
        path.join(__dirname, '..', 'node_modules', 'diffhtml')
    ]
};

exports.handlebarsRule = {
    test: /\.hbs/,
    use: [{
        loader: 'handlebars-loader',
        query: {
            helperDirs: [path.join(client, 'helpers')],
            partialDirs: [client],
            preventIndent: true,
            compat: true
        }
    }]
};

exports.styleRule = {
    test: /\.(scss|css)$/,
    include: [
        path.join(pages, 'editor'),
        path.join(pages, 'tinymce'),
        path.join(client, 'component')
    ],
    use: [{
        loader: 'style-loader',
        options: {
            hmr: false
        }
    }, 'css-loader', 'sass-loader']
};

exports.usableStyleRule = {
    test: /\.(scss|css)$/,
    include: [
        path.join(pages, 'settings'),
        path.join(client, 'prism'),
        /node_modules/
    ],
    use: [{
        loader: 'style-loader/useable',
        options: {
            hmr: false
        }
    }, 'css-loader', 'sass-loader']
};

exports.resolve = {
    alias: {
        redux: 'redux/es',
        brookjs: 'brookjs/es',
        'brookjs-silt': 'brookjs-silt/es'
    }
};

exports.styleLintPlugin = new StyleLintPlugin({
    syntax: 'scss'
});

exports.notifierPlugin = new WebpackNotifierPlugin({
    alwaysNotify: true
});

exports.copyPlugin = new CopyWebpackPlugin([{
    from: 'node_modules/prismjs/components/*.js',
    flatten: true,
}]);

const flowOut = msg => stdout => {
    console.log(stdout); // eslint-disable-line no-console

    notifier.notify({ title: 'Flow', message: msg });
};

exports.flowPlugin = new FlowStatusWebpackPlugin({
    binaryPath: flowPath,
    onSuccess: flowOut('Flow passed'),
    onError: flowOut('Flow failed')
});
