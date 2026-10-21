import inertia from "@inertiajs/vite";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import babel from "@rolldown/plugin-babel";
import tailwindcss from "@tailwindcss/vite";
import react, { reactCompilerPreset } from "@vitejs/plugin-react";
import laravel from "laravel-vite-plugin";
import { defineConfig, lazyPlugins } from "vite-plus";

export default defineConfig({
    /**
     * Formatting configuration.
     *
     * @see https://viteplus.dev/config/#configuring-vite
     */
    fmt: {
        sortImports: true,
        printWidth: 120,
        tabWidth: 4,
        singleQuote: false,
        semi: true,
        singleAttributePerLine: false,
        htmlWhitespaceSensitivity: "css",
        ignorePatterns: [
            ".agents/**",
            ".ai/**",
            ".claude/**",
            ".cursor/**",
            ".github/**",
            ".junie/**",
            ".vscode/**",
            "vendor/**",
            "node_modules/**",
            "public/**",
            "bootstrap/ssr/**",
            "composer.json",
            "pint.json",
            "skills-lock.json",
            "tailwind.config.js",
            "**/*.md",
            "resources/js/actions/**",
            "resources/js/routes/**",
            "resources/js/wayfinder/**",
            "resources/js/types/generated/**",
        ],
        sortTailwindcss: {
            functions: ["cn", "cva"],
            entryPoint: "resources/css/app.css",
        },
    },

    /**
     * Lint configuration.
     *
     * @see https://viteplus.dev/config/#configuring-vite
     */
    lint: {
        ignorePatterns: [
            ".agents/**",
            ".ai/**",
            ".claude/**",
            ".cursor/**",
            ".github/**",
            ".junie/**",
            ".vscode/**",
            "vendor/**",
            "node_modules/**",
            "public/**",
            "bootstrap/ssr/**",
            "tailwind.config.js",
            "resources/js/actions/**",
            "resources/js/routes/**",
            "resources/js/wayfinder/**",
            "resources/js/types/generated/**",
        ],
        options: {
            denyWarnings: true,
            typeAware: true,
        },
    },

    /**
     * Plugins configuration.
     *
     * @see https://viteplus.dev/config/#configuring-vite
     */
    plugins: lazyPlugins(() => [
        babel({
            presets: [reactCompilerPreset()],
        }),
        // The production SSR server listens on loopback only: PHP on the
        // same droplet is its one client (Inertia binds 0.0.0.0 by default).
        inertia({ ssr: { host: "127.0.0.1" } }),
        laravel({
            input: ["resources/css/app.css", "resources/js/app.tsx", "resources/css/administration/theme.css"],
            ssr: "resources/js/app.tsx",
            refresh: true,
        }),
        react(),
        tailwindcss(),
        wayfinder({ formVariants: true }),
        /**
         * The SSR bundle carries every dependency inside it: the deploy uploads
         * no node_modules, so the server bundle cannot import from it. Build
         * only: in dev the module runner would evaluate CommonJS packages
         * (React) as ESM and fail with "module is not defined".
         */
        {
            name: "kit:ssr-bundle-dependencies",
            apply: "build",
            config: () => ({ ssr: { noExternal: true } }),
        },
    ]),

    server: {
        watch: {
            // .codegraph holds the CodeGraph daemon's Unix socket, which the
            // watcher cannot lstat inside the container (ENOTSUP) and dies on.
            ignored: [
                "**/.agents/**",
                "**/.claude/**",
                "**/.codegraph/**",
                "**/.cursor/**",
                "**/.junie/**",
                "**/storage/**",
                "**/vendor/**",
            ],
        },
    },
});
