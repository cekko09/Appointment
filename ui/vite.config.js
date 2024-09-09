import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    host: '0.0.0.0', // Docker ile kullanılabilmesi için host'u aç
    port: 8080, // Docker'daki port ile eşleşmeli
    watch: {
      usePolling: true, // Docker'ın dosya değişikliklerini algılaması için
    }
  }

})
