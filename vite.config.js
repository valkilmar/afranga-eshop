
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
server: {
    hmr: {
        host: "0.0.0.0",
    },
    port: 3000,
    host: true,
},
plugins: [
    laravel({
        input: ["resources/css/app.css", "resources/js/app.js"],
        refresh: true,
    }),
],
});





// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: [
//                 'resources/sass/app.scss',
//                 'resources/js/app.js',
//             ],
//             refresh: true,
//         }),
//     ],
// });
