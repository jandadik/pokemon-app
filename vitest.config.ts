import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    // Ignoruj CSS importy (pro Vuetify a další knihovny)
    alias: {
      '\\.css$': path.resolve('./tests/__mocks__/styleMock.js'),
    },
    globals: true,
  },
});
