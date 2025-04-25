import { format } from 'date-fns';
import { cs } from 'date-fns/locale';

/**
 * Získá URL obrázku karty s fallbacky.
 * @param {object} card Objekt karty.
 * @returns {string} URL obrázku nebo placeholder.
 */
export function getCardImageUrl(card) {
    if (!card) {
        return '/images/placeholder.jpg';
    }
    if (card.img_file_small) {
        const path = '/images/' + card.img_file_small.replace(/\\/g, '/');
        return path;
    }
    if (card.img_small) {
        return card.img_small;
    }
    if (card.set_id && card.number) {
        const localPath = `/images/card_images/${card.set_id}/${card.number}.png`;
        return localPath;
    }
    return '/images/placeholder.jpg';
}

/**
 * Vrátí název ikony pro daný typ energie.
 * @param {string} type Typ energie (Grass, Fire, atd.).
 * @returns {string} Název MDI ikony.
 */
export function getTypeIcon(type) {
    const icons = {
        'Colorless': 'mdi-circle-outline',
        'Darkness': 'mdi-moon-waning-crescent',
        'Dragon': 'mdi-dragon',
        'Fairy': 'mdi-butterfly',
        'Fighting': 'mdi-boxing-glove',
        'Fire': 'mdi-fire',
        'Grass': 'mdi-leaf',
        'Lightning': 'mdi-lightning-bolt',
        'Metal': 'mdi-anvil',
        'Psychic': 'mdi-eye',
        'Water': 'mdi-water',
    };
    return icons[type] || 'mdi-circle-outline';
}

/**
 * Vrátí název barvy pro daný typ energie.
 * @param {string} type Typ energie.
 * @returns {string} Název Vuetify barvy.
 */
export function getTypeColor(type) {
    const colors = {
        'Colorless': 'grey',
        'Darkness': 'purple-darken-3',
        'Dragon': 'amber-darken-2',
        'Fairy': 'pink-lighten-2',
        'Fighting': 'brown',
        'Fire': 'red',
        'Grass': 'green',
        'Lightning': 'yellow-darken-2',
        'Metal': 'grey-darken-1',
        'Psychic': 'purple',
        'Water': 'blue',
    };
    return colors[type] || 'grey';
}

/**
 * Vrátí CSS třídu pro danou vzácnost karty.
 * @param {string} rarity Vzácnost karty.
 * @returns {string} CSS třída.
 */
export function getRarityClass(rarity) {
    if (!rarity) return 'text-grey';
    const rarityKey = rarity.toLowerCase().replace(/\s+/g, '-');
    const classes = {
        'common': 'text-common',
        'uncommon': 'text-uncommon',
        'rare': 'text-rare',
        'rare-holo': 'text-rare-holo',
        'rare-ultra': 'text-rare-ultra',
        'rare-secret': 'text-rare-secret',
        'amazing-rare': 'text-rare-holo',
        'ultra-rare': 'text-rare-ultra',
        'secret-rare': 'text-rare-secret',
        'promo': 'text-uncommon',
    };
    return classes[rarityKey] || 'text-grey';
}

/**
 * Formátuje číslo karty (např. doplní nuly).
 * @param {string|number} number Číslo karty.
 * @returns {string} Formátované číslo.
 */
export function formatCardNumber(number) {
    if (!number) return '000';
    const numericPart = String(number).replace(/\D/g, '');
    return numericPart.padStart(3, '0');
}

/**
 * Formátuje číselnou cenu jako desetinné číslo.
 * @param {string|number|null|undefined} price Cena.
 * @returns {string} Formátovaná cena nebo '-'.
 */
export function formatNumberPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    const numericPrice = parseFloat(price);
    if (isNaN(numericPrice)) {
        console.warn('formatNumberPrice: Neplatná hodnota ceny:', price);
        return 'N/A'; 
    }
    return new Intl.NumberFormat('cs-CZ', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numericPrice);
}

/**
 * Formátuje číselnou cenu jako měnu (EUR).
 * @param {string|number|null|undefined} price Cena.
 * @returns {string} Formátovaná cena nebo '-'.
 */
export function formatPrice(price) {
    if (price === null || price === undefined) {
        return '-';
    }
    const numericPrice = parseFloat(price);
    if (isNaN(numericPrice)) {
        console.warn('formatPrice: Neplatná hodnota ceny:', price);
        return 'N/A'; 
    }
    return new Intl.NumberFormat('cs-CZ', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numericPrice);
}

/**
 * Formátuje datum (string) do čitelného formátu.
 * @param {string|null|undefined} dateString Datum jako string.
 * @returns {string} Formátované datum nebo prázdný string.
 */
export function formatUpdateDate(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        // Zkontrolujeme, zda je datum platné
        if (isNaN(date.getTime())) {
            console.warn('formatUpdateDate: Neplatný formát data:', dateString);
            return 'N/A';
        }
        return format(date, 'd. MMMM yyyy', { locale: cs });
    } catch (e) {
        console.error('Chyba při formátování data:', dateString, e);
        return 'N/A';
    }
}

/**
 * Handler pro chybu načítání obrázku, nastaví placeholder.
 * @param {Event} event Událost chyby obrázku.
 */
export function handleImageError(event) {
    console.warn('Chyba načítání obrázku, nahrazuji placeholderem:', event.target.src);
    event.target.src = '/images/placeholder.jpg';
}

/**
 * Získá relevantní hodnotu ceny pro zobrazení.
 * Priorita: price_cm_avg30 -> price_cm_trend -> price_tcg_market
 * @param {object} item Objekt karty s přímými cenovými vlastnostmi.
 * @returns {string} Formátovaná cena nebo '-'.
 */
export function getPriceValue(item) {
    if (item.price_cm_avg30 !== null && item.price_cm_avg30 !== undefined) {
        return formatNumberPrice(item.price_cm_avg30);
    }
    if (item.price_cm_trend !== null && item.price_cm_trend !== undefined) {
        return formatNumberPrice(item.price_cm_trend);
    }
    if (item.price_tcg_market !== null && item.price_tcg_market !== undefined) {
        // TCG ceny jsou v USD, zatím necháme bez převodu a formátujeme jako číslo
        // V budoucnu by bylo dobré přidat přepočet nebo označení měny
        return formatNumberPrice(item.price_tcg_market); // Nebo formatPrice s USD
    }
    // Starší fallbacky, pokud by existovaly
    if (item.price !== null && item.price !== undefined) {
        return formatNumberPrice(item.price);
    }
    if (item.prices && item.prices.avg !== null && item.prices.avg !== undefined) {
        return formatNumberPrice(item.prices.avg);
    }
    return '-';
} 