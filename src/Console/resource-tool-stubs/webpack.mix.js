let mix = require('laravel-mix')

mix.js('resources/js/tool.js', __dirname + 'dist/js')
   .sass('resources/sass/tool.scss', __dirname + 'dist/css')
