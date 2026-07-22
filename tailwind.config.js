import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // B2B + Perfumes dynamic colors (legacy light)
        ...['blue', 'yellow', 'amber', 'indigo', 'green', 'emerald', 'red', 'purple', 'gray', 'orange', 'pink', 'rose', 'violet'].flatMap(c => [
            `bg-${c}-50`, `bg-${c}-100`, `bg-${c}-200`,
            `text-${c}-600`, `text-${c}-700`, `text-${c}-800`,
            `border-${c}-200`,
            `bg-${c}-500`, `text-${c}-500`,
            `border-l-${c}-500`,
        ]),
        // Dark theme badges (ERP + B2B)
        ...['blue', 'yellow', 'amber', 'indigo', 'green', 'emerald', 'red', 'purple', 'gray', 'orange', 'pink', 'rose', 'violet'].flatMap(c => [
            `bg-${c}-500/10`, `bg-${c}-500/15`,
            `text-${c}-400`, `text-${c}-300`,
            `border-${c}-500/20`, `border-${c}-500/30`, `border-${c}-500/60`,
        ]),
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Geist"', '"Inter"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'dg': {
                    50: '#f7f7f7',
                    100: '#e3e3e3',
                    200: '#c8c8c8',
                    300: '#a4a4a4',
                    400: '#818181',
                    500: '#666666',
                    600: '#515151',
                    700: '#434343',
                    800: '#383838',
                    900: '#1a1a1a',
                    950: '#0d0d0d',
                },
                surface: {
                    DEFAULT: '#0d0d0d',
                    raised: '#141414',
                    overlay: '#1a1a1a',
                    elevated: '#222222',
                },
                border: {
                    DEFAULT: 'rgba(255,255,255,0.06)',
                    subtle: 'rgba(255,255,255,0.04)',
                    strong: 'rgba(255,255,255,0.12)',
                },
            },
        },
    },

    plugins: [forms],
};
