/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './node_modules/flowbite/**/*.js',
    ],
    theme: {
        extend: {
            fontFamily: {
                castoro: ['Castoro', 'serif'],
                outfit: ['Outfit', 'sans-serif'],
                overpass: ['Overpass', 'sans-serif'],
                poppins: ['Poppins', 'sans-serif'],
                ubuntu: ['Ubuntu', 'sans-serif'],
            },
            width: Object.fromEntries(
                Array.from({ length: 99 }, (_, index) => {
                    const value = index + 2;
                    return [String(value), `${value / 4}rem`];
                })
            ),
            colors: {
                transparent: 'transparent',
                current: 'currentColor',
                black: colors.black,
                white: colors.white,
                rose: colors.rose,
                pink: colors.pink,
                fuchsia: colors.fuchsia,
                purple: colors.purple,
                violet: colors.violet,
                indigo: colors.indigo,
                blue: colors.blue,
                lightBlue: colors.sky,
                cyan: colors.cyan,
                teal: colors.teal,
                emerald: colors.emerald,
                green: colors.green,
                lime: colors.lime,
                yellow: colors.yellow,
                amber: colors.amber,
                orange: colors.orange,
                red: colors.red,
                neutral: colors.slate,
                zinc: colors.zinc,
                gray: colors.gray,
                stone: colors.stone,
            },
        },
    },
    plugins: [require('flowbite/plugin')],
};
