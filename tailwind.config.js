import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            gridTemplateColumns: {
                '20': 'repeat(20, minmax(0, 1fr))',
                '40': 'repeat(40, minmax(0, 1fr))',
            },
            colors: {
                'vulcan': {
                    '50': '#f4f6fb',
                    '100': '#e8ecf6',
                    '200': '#ccd7eb',
                    '300': '#9fb5da',
                    '400': '#6b8dc5',
                    '500': '#486faf',
                    '600': '#365793',
                    '700': '#2d4677',
                    '800': '#283d64',
                    '900': '#263554',
                    '950': '#0e131f',
                },
            }
        },
    },

    plugins: [forms],
};
