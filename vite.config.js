import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import fs from "fs";
import { homedir } from "os";
import path from "path";

const host = "comms.test";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true, // Match Laravel’s IP
        port: 5173,
        https: {
            key: fs.readFileSync(
                path.resolve(
                    homedir(),
                    `Library/Application Support/Herd/config/valet/Certificates/${host}.key`,
                ),
            ),
            cert: fs.readFileSync(
                path.resolve(
                    homedir(),
                    `Library/Application Support/Herd/config/valet/Certificates/${host}.crt`,
                ),
            ),
        },
    },
});
