const mix = require('laravel-mix');
const WebpackShellPlugin = require('webpack-shell-plugin'),
      CompressionPlugin = require('compression-webpack-plugin'),
      OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

mix.extend('addWebpackLoaders', (webpackConfig, loaderRules) => {
    loaderRules.forEach((loaderRule) => webpackConfig.module.rules.push(loaderRule));
});

mix.js('resources/client-js-shotsvip/app.js', 'public/js')
//mix.js('resources/admin-js/app.js', 'public/js/admin')
    .sass('resources/sass/app.scss', 'public/css')
//.sass('resources/sass/admin/app.scss', 'public/css/admin')
    .sass('resources/sass/error.scss', 'public/css')
    .copy('resources/img-shotsvip', 'public/img')
    .copy('resources/sounds-shotsvip', 'public/sounds')
    .options({
        processCssUrls: false
    })
    .version()
    .webpackConfig({
        plugins: [
            new WebpackShellPlugin({
                onBuildStart: ['php artisan cache:clear --quiet'],
                onBuildEnd: ['php artisan dk:onBuildEnd']
            }),
            new OptimizeCssAssetsPlugin({
                assetNameRegExp: /\.optimize\.css$/g,
                cssProcessor: require('cssnano'),
                cssProcessorPluginOptions: {
                    //Default
                    preset: ['advanced', { discardComments: { removeAll: true } }],
                },
                canPrint: true
            }),
            new CompressionPlugin({
                filename: '[path].gz[query]',
                algorithm: 'gzip',
                test: /\.js$|\.css$|\.html$|\.eot?.+$|\.ttf?.+$|\.woff?.+$|\.svg?.+$/,
                threshold: 10,
                minRatio: 0.6,
                cache: false
            })
        ]
    })
    .addWebpackLoaders([
        {
            test: /\.key|\.txt$/i,
            use: "raw-loader"
        }
    ]);
