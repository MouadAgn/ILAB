const Encore = require('@symfony/webpack-encore');

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path used by the web server to access the output path
    .setPublicPath('/build')
    // Only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // Add Bootstrap and other scripts to be compiled
    .addEntry('app', './assets/js/app.js')

    // Will require an additional script tag for jQuery
    .autoProvidejQuery()

    // Enables Sass/SCSS support (from step 3)
    .enableSassLoader()
    // Enables versioning of built files (recommended)
    .enableVersioning(Encore.isProduction())

    // Enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
;

module.exports = Encore.getWebpackConfig();
