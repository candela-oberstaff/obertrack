// import defaultTheme from 'tailwindcss/defaultTheme';
// import forms from '@tailwindcss/forms';

// /** @type {import('tailwindcss').Config} */
// export default {
//     content: [
//         './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
//         './storage/framework/views/*.php',
//         './resources/views/**/*.blade.php',
//     ],

//     theme: {
//         extend: {
//             fontFamily: {
//                 // sans: ['Figtree', ...defaultTheme.fontFamily.sans],
//                 'sans': ['Montserrat', 'sans-serif'],
//             },
//         },
//     },

//     plugins: [forms],
// };


import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    prefix: 'tw-',
	important: false,
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/**/*html, jsx, js',
		'./resources/views/*.js',
		'./resources/views/*.html'
    ],

    theme: {
        extend: {
            fontFamily: {
                // sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                'sans': ['Montserrat', 'sans-serif'],
            },
            colors: {
				primary: "#4f55c1"
			}
        },
    },

    plugins: [forms],
};
