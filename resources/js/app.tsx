import { createInertiaApp, router } from "@inertiajs/react";
import type { PageProps as ModalPageProps } from "@inertiaui/modal-react";
import { putConfig, renderApp } from "@inertiaui/modal-react";
import { LaravelReactI18nProvider as I18nProvider } from "laravel-react-i18n";
import { StrictMode } from "react";
import { createRoot, hydrateRoot } from "react-dom/client";

import { loadInitialDictionaries, translations } from "./lib/i18n";

/**
 * The app's name, taken from the shared `variables` prop when the app is
 * set up, so it follows the server's configuration instead of the build's.
 */
let appName = "";

/**
 * Enable URL updates when opening modals so direct modal links keep working.
 */
putConfig({ navigate: true });

/**
 * Executed whenever a flash message is provided from
 * the server.
 */
router.on("flash", () => {
    // @todo: Implement after shadcn's toast component
    // is installed.
});

// Load the dictionary (or, on the server, every dictionary) the app starts in.
await loadInitialDictionaries();

/**
 * Create the Inertia app.
 */
void createInertiaApp({
    /**
     * The app's progress bar configuration.
     */
    progress: {
        color: "oklch(0.527 0.154 150.069)",
        delay: 0,
    },

    /**
     * Mount the Inertia app using renderApp so ModalRoot is always present at the
     * app root (inside PageContext) on first paint, enabling direct modal URL visits.
     */
    setup({ el, App, props }) {
        const locale = (props.initialPage.props.locale as string | undefined) ?? "en";

        // Create the app to render.
        const app = (
            <StrictMode>
                <I18nProvider locale={locale} fallbackLocale="en" files={translations}>
                    {renderApp(App as unknown as Parameters<typeof renderApp>[0], props as ModalPageProps)}
                </I18nProvider>
            </StrictMode>
        );

        // Only the docs arrive server-rendered; every other page is
        // rendered here from scratch.
        if (el) {
            if (el.hasAttribute("data-server-rendered")) {
                hydrateRoot(el, app);
            } else {
                createRoot(el).render(app);
            }
        }

        return app;
    },

    /**
     * Format the document title: the page's own title (set with <Head title>)
     * followed by the app's name, or the app's name alone when the page has none.
     */
    title: (title) => (title ? `${title} | ${appName}` : appName),
});
