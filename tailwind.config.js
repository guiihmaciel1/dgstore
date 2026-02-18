import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // B2B dynamic colors: order statuses, retailer status, badges
        ...['blue', 'yellow', 'amber', 'indigo', 'green', 'emerald', 'red', 'purple', 'gray', 'orange', 'pink'].flatMap(c => [
            `bg-${c}-50`, `bg-${c}-100`, `bg-${c}-200`,
            `text-${c}-600`, `text-${c}-700`, `text-${c}-800`,
            `border-${c}-200`,
            `bg-${c}-500`, `text-${c}-500`,
        ]),
        // User role badges + avatar initials (dark theme with opacity modifiers)
        ...['purple', 'blue', 'green', 'indigo', 'pink'].flatMap(c => [
            `bg-${c}-500/10`, `bg-${c}-500/15`,
            `text-${c}-400`,
            `border-${c}-500/20`, `border-${c}-500/30`, `border-${c}-500/60`,
        ]),
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Paleta DG Store - baseada na logo
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
            },
        },
    },

    plugins: [forms],
};
