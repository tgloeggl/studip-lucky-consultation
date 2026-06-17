const path = require('path');
const fs = require('fs');

const rspack = require('@rspack/core');
const { VueLoaderPlugin } = require('vue-loader');

class StudipCourseViewPlugin {
    constructor(options) {
        this.template = options.template;
        this.filename = options.filename;
    }

    apply(compiler) {
        compiler.hooks.afterEmit.tap('StudipCourseViewPlugin', compilation => {
            const entrypoint = compilation.entrypoints.get('main');
            const assets = entrypoint
                ? entrypoint.getFiles()
                : compilation.getAssets().map(asset => asset.name);
            const stylesheets = assets
                .filter(asset => asset.endsWith('.css'))
                .map(asset => `    <? PageLayout::addStylesheet($this->plugin->getPluginUrl() . '/static/${asset}'); ?>`);
            const scripts = assets
                .filter(asset => asset.endsWith('.js'))
                .map(asset => `    <? PageLayout::addScript($this->plugin->getPluginUrl() . '/static/${asset}'); ?>`);
            const bundleTags = ['<!-- load bundles -->', ...stylesheets, ...scripts, '<!-- END load bundles -->'].join('\n');
            const template = fs.readFileSync(path.resolve(__dirname, this.template), 'utf8');
            const output = template.replace(/<!-- load bundles -->[\s\S]*?<!-- END load bundles -->/, bundleTags);
            const filename = path.resolve(__dirname, this.filename);

            fs.mkdirSync(path.dirname(filename), { recursive: true });
            fs.writeFileSync(filename, output);
        });
    }
}

module.exports = () => ({
    entry: ['./vueapp/app.js', './assets/luckyconsultation.scss'],
    output: {
        filename: '[name].[contenthash].js',
        path: path.resolve(__dirname, 'static'),
        publicPath: '/',
        clean: true
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
            {
                test: /\.css$/,
                use: [
                    rspack.CssExtractRspackPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            importLoaders: 1
                        }
                    },
                    'postcss-loader'
                ]
            },
            {
                test: /\.scss$/,
                use: [
                    rspack.CssExtractRspackPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false,
                            importLoaders: 2
                        }
                    },
                    'postcss-loader',
                    'sass-loader'
                ]
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin(),
        new StudipCourseViewPlugin({
            template: 'vueapp/templates/course_index.php',
            filename: 'app/views/course/index.php'
        }),
        new rspack.CssExtractRspackPlugin({
            filename: '[name].css',
            chunkFilename: '[name].css?h=[chunkhash]'
        })
    ],
    resolve: {
        extensions: ['.vue', '.js'],
        alias: {
            '@': path.resolve(__dirname, 'vueapp'),
            '@studip': path.resolve(__dirname, 'vueapp/components/Studip'),
            '@popperjs/core': path.resolve(__dirname, 'node_modules/@popperjs/core')
        }
    }
});
