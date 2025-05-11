# Uživatelské testovací scénáře – Správa sbírek

Tento dokument obsahuje doporučené scénáře pro ruční ověření funkčnosti správy sbírek v aplikaci.

---

## 1. Základní CRUD operace

### 1.1 Vytvoření sbírky
- Přejít na stránku se seznamem sbírek
- Kliknout na "Vytvořit sbírku"
- Vyplnit název (např. "Testovací sbírka")
- Přidat popis (např. "Sbírka pro testování funkčnosti")
- Nastavit jako veřejnou
- Uložit
- **Očekávaný výsledek:** Přesměrování na detail sbírky, zobrazení notifikace o úspěšném vytvoření

### 1.2 Zobrazení sbírky
- Přejít na seznam sbírek
- Kliknout na název nebo ikonu oka u "Testovací sbírka"
- **Očekávaný výsledek:** Zobrazení detailu sbírky se správnými údaji

### 1.3 Úprava sbírky
- Na stránce detailu sbírky kliknout na "Upravit"
- Změnit název, popis, viditelnost
- Uložit
- **Očekávaný výsledek:** Přesměrování zpět na detail s aktualizovanými údaji

### 1.4 Smazání sbírky
- Na stránce detailu kliknout na "Smazat"
- Potvrdit smazání v dialogu
- **Očekávaný výsledek:** Přesměrování na seznam sbírek, sbírka již není v seznamu

---

## 2. Speciální funkce

### 2.1 Nastavení výchozí sbírky
- Vytvořit dvě sbírky
- Na detailu jedné kliknout na "Nastavit jako výchozí"
- Ověřit označení "Výchozí"
- Nastavit druhou sbírku jako výchozí
- **Očekávaný výsledek:** První sbírka již není výchozí, druhá je označena jako výchozí

### 2.2 Změna viditelnosti
- Vytvořit veřejnou sbírku
- Na detailu změnit viditelnost pomocí tlačítka "Zneveřejnit sbírku"
- **Očekávaný výsledek:** Změna textu tlačítka a indikátoru viditelnosti

---

## 3. Validace formulářů

### 3.1 Prázdný formulář
- Přejít na vytvoření nové sbírky
- Nechat pole prázdná a odeslat
- **Očekávaný výsledek:** Zobrazení chybových hlášek u povinných polí

### 3.2 Příliš dlouhý název
- Vyplnit do pole název text delší než 255 znaků
- **Očekávaný výsledek:** Zobrazení chybové hlášky o překročení délky

---

## 4. Testování oprávnění

### 4.1 Nepřihlášený uživatel
- Odhlásit se
- Pokusit se přejít na stránku sbírek
- **Očekávaný výsledek:** Přesměrování na přihlašovací obrazovku

### 4.2 Cizí sbírky
- Přihlásit se jako uživatel A
- Vytvořit sbírku
- Přihlásit se jako uživatel B
- Pokusit se zobrazit soukromou sbírku uživatele A
- **Očekávaný výsledek:** Chyba přístupu nebo přesměrování 