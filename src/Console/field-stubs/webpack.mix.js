let mix = require('laravel-mix')

mix.js('resources/js/field.js', __dirname + 'dist/js')
   .sass('resources/sass/field.scss', __dirname + 'dist/css')
