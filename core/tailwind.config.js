/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/js/vue/**/*.vue",
        "./Modules/Pos/vue/**/*.vue",
        "./Modules/Pos/Resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
    corePlugins: {
        preflight: false,
    },
}
