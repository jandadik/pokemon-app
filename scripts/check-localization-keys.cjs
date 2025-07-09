#!/usr/bin/env node

/**
 * Localization Key Checker
 * Helps verify if translation keys exist before using them
 */

const fs = require('fs');
const path = require('path');

// Load Czech translations (main language)
const translationsPath = path.join(__dirname, '..', 'resources', 'lang', 'cs');

function loadTranslations() {
  const translations = {};
  const files = fs.readdirSync(translationsPath).filter(f => f.endsWith('.php'));
  
  files.forEach(file => {
    const filePath = path.join(translationsPath, file);
    const content = fs.readFileSync(filePath, 'utf8');
    
    // Basic PHP array parsing (works for simple cases)
    const filename = file.replace('.php', '');
    translations[filename] = content;
  });
  
  return translations;
}

function checkKey(key) {
  const translations = loadTranslations();
  const [file, ...keyParts] = key.split('.');
  const keyPath = keyParts.join('.');
  
  if (!translations[file]) {
    return { exists: false, reason: `File '${file}.php' not found` };
  }
  
  // Simple check if key appears in file content
  const pattern = new RegExp(`['"]${keyPath}['"]\\s*=>`);
  if (translations[file].match(pattern)) {
    return { exists: true };
  }
  
  return { exists: false, reason: `Key '${keyPath}' not found in ${file}.php` };
}

// Predefined correct keys for quick reference
const verifiedKeys = {
  // Table headers
  'collections.fields.name': '✅ Verified',
  'collections.fields.actions': '✅ Verified',
  'collections.cards.table.condition': '✅ Verified',
  'collections.cards.table.quantity': '✅ Verified', 
  'collections.form.language': '✅ Verified',
  
  // Buttons
  'collections.buttons.edit': '✅ Verified',
  'collections.buttons.delete': '✅ Verified',
  'collections.buttons.create': '✅ Verified',
  
  // Empty states
  'collections.items.empty_title': '✅ Verified',
  'collections.items.empty_text': '✅ Verified',
  
  // UI elements
  'ui.dialogs.delete': '✅ Verified',
  'ui.messages.confirm_delete': '✅ Verified',
  'ui.messages.action_irreversible': '✅ Verified',
  'ui.buttons.cancel': '✅ Verified',
  'ui.buttons.delete': '✅ Verified',
  
  // FORBIDDEN keys (will cause errors)
  'collections.fields.condition': '❌ FORBIDDEN - Use collections.cards.table.condition',
  'collections.fields.quantity': '❌ FORBIDDEN - Use collections.cards.table.quantity',
  'collections.fields.language': '❌ FORBIDDEN - Use collections.form.language',
};

// Command line interface
if (require.main === module) {
  const args = process.argv.slice(2);
  
  if (args.length === 0) {
    console.log('\n🎯 Localization Key Checker\n');
    console.log('Usage: node scripts/check-localization-keys.js <key1> [key2] [key3]...\n');
    console.log('✅ VERIFIED KEYS:');
    Object.entries(verifiedKeys).forEach(([key, status]) => {
      if (status.includes('✅')) {
        console.log(`  ${key}`);
      }
    });
    console.log('\n❌ FORBIDDEN KEYS:');
    Object.entries(verifiedKeys).forEach(([key, status]) => {
      if (status.includes('❌')) {
        console.log(`  ${key} - ${status.split(' - ')[1]}`);
      }
    });
    console.log('\nExample: node scripts/check-localization-keys.js collections.fields.name ui.buttons.cancel\n');
    process.exit(0);
  }
  
  console.log('\n🔍 Checking translation keys...\n');
  
  args.forEach(key => {
    if (verifiedKeys[key]) {
      console.log(`${key}: ${verifiedKeys[key]}`);
    } else {
      const result = checkKey(key);
      if (result.exists) {
        console.log(`${key}: ✅ Found`);
      } else {
        console.log(`${key}: ❌ ${result.reason}`);
      }
    }
  });
  
  console.log('\n💡 Tip: Always reference Index.vue for correct patterns');
  console.log('📖 See .cursor/rules/localization.mdc for complete rules\n');
}

module.exports = { checkKey, verifiedKeys }; 