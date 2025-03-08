import { createI18n } from 'vue-i18n'
import { usePage } from '@inertiajs/vue3'

/**
 * Inicializace vue-i18n s překlady z backendu
 * 
 * @param {Object} initialTranslations Počáteční překlady (defaultně prázdný objekt)
 * @param {string} locale Aktuální jazyk
 * @returns {Object} Vue I18n instance
 */
export function setupI18n(initialTranslations = {}, locale = 'cs') {
    // Vytvoření vue-i18n instance
    const i18n = createI18n({
        legacy: false, // Používáme Composition API
        globalInjection: true, // Globální injekce $t, $tc atd.
        locale: locale, // Výchozí jazyk
        fallbackLocale: 'en', // Záložní jazyk
        messages: {
            [locale]: initialTranslations
        },
        // Další konfigurace
        allowComposition: true,
        warnHtmlMessage: false,
        datetimeFormats: {},
        numberFormats: {}
    })

    // Vytvoříme vlastní wrapper, který vynechá registraci v-t direktivy
    // když budeme registrovat plugin v app.js
    const customI18n = {
        install(app) {
            // Pouze registrujeme globální vlastnosti, ale bez v-t direktivy
            app.config.globalProperties.$t = i18n.global.t;
            app.config.globalProperties.$d = i18n.global.d;
            app.config.globalProperties.$n = i18n.global.n;
            app.provide('i18n', i18n.global);
        }
    }

    // Vracíme wrapper místo původního i18n
    return customI18n;
}

/**
 * Získání překladu podle klíče
 * 
 * @param {string} key Klíč překladu ve formátu file.section.key
 * @param {Object} params Parametry pro překlad
 * @returns {string} Přeložený text
 */
export function trans(key, params = {}) {
    try {
        // Získání aktuální stránky z Inertia
        const page = usePage()
        
        // Pokud nemáme překlady nebo klíč, vrátíme klíč
        if (!page || !page.props || !page.props.translations || !key) {
            return key
        }
        
        // Rozdělíme klíč na části (např. 'app.buttons.save' -> ['app', 'buttons', 'save'])
        const parts = key.split('.')
        
        // První část je soubor (app, auth, ui, validation)
        const file = parts.shift()
        
        // Zbývající části tvoří cestu k překladu
        const path = parts.join('.')
        
        // Zkontrolujeme, zda existuje překlad pro daný soubor
        if (!page.props.translations[file]) {
            return key
        }
        
        // Získáme překlad podle cesty
        let translation = page.props.translations[file]
        
        // Procházíme cestu k překladu
        for (const part of parts) {
            if (!translation[part]) {
                return key
            }
            translation = translation[part]
        }
        
        // Pokud je překlad řetězec, vrátíme ho
        if (typeof translation === 'string') {
            // Nahradíme parametry v překladu
            return replaceParams(translation, params)
        }
        
        // Pokud se nepodařilo najít překlad, vrátíme klíč
        return key
    } catch (error) {
        console.error('Chyba při překladu:', error)
        return key
    }
}

/**
 * Nahrazení parametrů v překladu
 * 
 * @param {string} text Text s placeholdery (:param)
 * @param {Object} params Parametry pro nahrazení
 * @returns {string} Text s nahrazenými parametry
 */
function replaceParams(text, params) {
    // Nahradíme parametry v překladu
    return Object.keys(params).reduce((result, param) => {
        return result.replace(new RegExp(`:${param}`, 'g'), params[param])
    }, text)
}

/**
 * Plugin pro Vue, který poskytuje funkci trans() v template
 */
export const TranslationPlugin = {
    install(app) {
        app.config.globalProperties.$trans = trans
    }
} 