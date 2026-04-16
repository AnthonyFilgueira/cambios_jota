import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'cj': {
                    'morado-profundo': '#7C3AED',
                    'morado-medio': '#A78BFA',
                    'morado-claro': '#DDD6FE',
                    'turquesa': '#06B6D4',
                    'turquesa-claro': '#67E8F9',
                    'rosa': '#EC4899',
                    'fondo': '#F8FAFC',
                    'texto': '#1E293B',
                    'texto-claro': '#64748B',
                },
            },
        },
    },

    plugins: [forms],
};
