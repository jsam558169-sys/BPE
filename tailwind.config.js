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
            colors: {
                'brand-navy': '#0D2B45',
                'brand-orange': '#E8761A',
                'brand-sky': '#3A9BC4',
                'neutral-dark': '#050505',
                'neutral-body': '#495057',
                'app-bg': '#F8F9FA',
            },
            fontFamily: {
                'header': ['Inter', 'Barlow', ...defaultTheme.fontFamily.sans],
                'body': ['Inter', 'Source Sans Pro', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
