import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    // base: "/",
    // server: {
    //     host: "0.0.0.0",
    //     port: 8000,
    //     hmr: {
    //         host: "192.168.88.210", // Change this value for your local network ip address
    //         port: 8080, // Or your app's standard port
    //     },
    // },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
