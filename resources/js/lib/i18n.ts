/**
 * The dictionaries, one loader per locale, each its own chunk.
 */
const dictionaries = import.meta.glob("/lang/*.json");

/**
 * The path of a locale's dictionary, as the i18n provider keys it.
 */
const pathFor = (locale: string): string => `/lang/${locale}.json`;

/**
 * The translation dictionaries handed to the i18n provider, keyed by path.
 *
 * The provider keeps a reference to this very object, so a dictionary added
 * to it later (see `loadDictionary`) is one the provider can switch to
 * without the page reloading.
 */
export const translations: Record<string, unknown> = {};

/**
 * Fetch the dictionaries at the given paths into `translations`.
 */
const load = async (paths: Array<string>): Promise<void> => {
    const loaded = await Promise.all(paths.map(async (path) => [path, await dictionaries[path]()] as const));

    for (const [path, dictionary] of loaded) {
        translations[path] = dictionary;
    }
};

/**
 * Load the dictionaries the app starts with.
 *
 * The server renders every locale from one process, so it loads them all.
 * The browser only ever starts in one, so it downloads that one alone
 * instead of shipping every language inside the main bundle on every visit.
 * The server sets `<html lang>` from the request's locale before any script
 * runs, which is what tells the browser which one to fetch.
 */
export const loadInitialDictionaries = async (): Promise<void> =>
    load(
        import.meta.env.SSR
            ? Object.keys(dictionaries)
            : [pathFor(document.documentElement.lang.split("-")[0] || "en")].filter((path) => path in dictionaries),
    );

/**
 * Make sure the given locale's dictionary is loaded, so the provider can
 * switch to it in place, as the docs do when the reader changes language.
 */
export const loadDictionary = async (locale: string): Promise<void> => {
    const path = pathFor(locale);

    if (path in translations || !(path in dictionaries)) {
        return;
    }

    await load([path]);
};
