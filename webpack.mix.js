const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const autoprefixer = require('autoprefixer');

mix.options({ progress: false })
    .js('resources/js/app.js', 'public/js')
    .js('resources/js/product-search.js', 'public/js')
    .js('resources/js/not-ready.js', 'public/js')
    .js('resources/js/toast.js', 'public/js')
    .js('resources/js/user-tabs.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        tailwindcss('./tailwind.config.js'),
        autoprefixer(),
    ]);
