const Encore = require('@symfony/webpack-encore');

Encore
// the project directory where compiled assets will be stored

    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')


    .addEntry('app', './assets/src/index.js')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    .enableBuildNotifications()

    .enableReactPreset()
;

module.exports = Encore.getWebpackConfig();
