const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix
    //.js('resources/js/app.js', 'public/js')
    .js('resources/js/admin/custom.js', 'public/js/admin/custom.min.js')
    //.sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/admin/custom.scss', 'public/css/admin/custom.min.css')
    .sass('resources/sass/admin/login.scss', 'public/css/admin/login.min.css')
    .copyDirectory('resources/images/admin', 'public/images/admin')
    .version();
