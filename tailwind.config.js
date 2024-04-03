import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './vendor/wire-elements/modal/**/*.blade.php',
    ],

    options: {
        safelist: [
            "sm:max-w-sm",
            "sm:max-w-md",
            "sm:max-w-lg",
            "sm:max-w-xl",
            "sm:max-w-2xl",
            "sm:max-w-3xl",
            "sm:max-w-4xl",
            "sm:max-w-5xl",
            "sm:max-w-6xl",
            "sm:max-w-7xl"
        ]
    },

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
