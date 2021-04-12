const mix = require('laravel-mix');

// mix.webpackConfig({ resolve: { symlinks: false } }) 

mix.js('resources/js/app.js', 'public/js')   
   .js('resources/js/dashboard.js', 'public/js').vue()
   .options({ processCssUrls: false });

