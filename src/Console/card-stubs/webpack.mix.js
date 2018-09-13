let mix = require('laravel-mix')

mix.js('resources/js/card.js', __dirname + 'dist/js')
   .sass('resources/sass/card.scss', __dirname + 'dist/css')
