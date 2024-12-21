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
            boxShadow: {
                'l': '0 0 56px 0 rgba(0,0,0,0.04)',
                'xl': '0 0 32px 0 rgba(0,0,0,0.08)',
                '2xl': '0 0 56px rgba(0, 0, 0, 0.12)',
                '3xl': '0 6px 78px rgba(0, 0, 0, 0.08)',
                '4xl': '0 6px 78px rgba(0, 0, 0, 0.24)',
                '5xl': '0 -4px 48px rgba(0, 0, 0, 0.08)',
            },
            colors: {
                'arris': {
                    'actionBtn': {
                        'primary': '#4C1D95',
                        'primaryHover': '#360F73',
                    },
                    'btn': {
                        'primary': '#3970BE',
                        'primaryHover': '#1F345B',
                        'secondary': '#EDE9FE',
                        'secondaryHover': '#DBCFFF',
                        'warning': '#ff7f0e',
                        'warningHover': '#c15a00',
                        'dark': '#222222',
                        'darkHover': '#000000',
                        'danger': '#db0008',
                        'dangerHover': '#c20007',
                        'success': '#23923d',
                        'successHover': '#19692c',
                        'textPrimary': '#FFFFFF',
                        'textSecondary': '#4C1D95',
                    },
                    'crm': {
                        'fail': '#FFE7E6',
                        'primaryColor': '#E6ECF8',
                        'secondaryColor': '#C7D6F0',
                        'tertiaryColor': '#28559F',
                        'success': '#DDFBE0',
                        'text': '#090F1B',
                    },
                    'inputBox': {
                        'background': '#FFFFFF',
                        'border': '#E6ECF8',
                        'fill': '#4C1D95',
                        'radioboxBorder': '#5D8ED3',
                        'radioboxFill': '#5D8ED3',
                        'textFieldBackground': '#F3F5FC',
                    },
                    'general': {
                        'error': '#B91C1C',
                    },
                    'modal': {
                        'contentBackground': '#FFFFFF',
                        'tint': '#000C',
                        'topBarTextColor': '#FFFFFF',
                        'topBarBackground': '#000000',

                    },
                    'searchbox': {
                        'border': '#3970BE',
                        'chosenOption': '#ededed',
                        'option-bg': '#efefef',
                        'option-bg-hover': '#dddddd'
                    },
                    'snackbar': {
                        'errorBg': '#db0008',
                        'errorText': '#FFFFFF',
                        'infoBg': '#3970BE',
                        'infoText': '#FFFFFF',
                        'successBg': '#23923d',
                        'successText': '#FFFFFF'
                    },
                    'textfield': {
                        'activeBorder': '#515254',
                        'border': '#E5E7EB',
                        'inputText': '#0E051B',
                        'placeholder': '#6B7280',
                    },
                    'background': '#F7F8FA',
                    'black': '#040920',
                    'blue': '#3044B8',
                    'blue-dark': '#2135AA',
                    'blue-light': '#D6DAF1',
                    'blue-hover': '#BFC9FF',
                    'blue-semi-light': '#E5E7F2',
                    'green500': '#22C55E',
                    'grey': '#6C739D',
                    'grey-dark': '#8B8C9A',
                    'grey-light': '#D8D8D8',
                    'grey100': '#F3F4F6',
                    'grey200': '#E5E7EB',
                    'grey300': '#6B7280',
                    'grey400': '#9CA3AF',
                    'grey500': '#6B7280',
                    'opacity': '#000000CC',
                    'pink': '#FFA0A0',
                    'red': '#F70041',
                    'red700': '#B91C1C',
                    'tint': '#000000CC',
                    'text': '#0E051B',
                    'violet100': '#EDE9FE',
                    'violet300': '#DBCFFF',
                    'violet400': '#A78BFA',
                    'violet700': '#6D28D9',
                    'violet900': '#4C1D95',
                    'violet900hover': '#360F73',
                    'white': '#FFFFFF',
                },
            },
            fontSize: {
                'text-base-small': [
                    '15px', {
                        lineHeight: '24px',
                        fontWeight: '400',
                    },
                ],
            },
            transitionDuration: {
                DEFAULT: '200ms',
            },
            transitionTimingFunction: {
                DEFAULT: 'ease-in-out',
            }
        },
    },

    plugins: [forms],
};
