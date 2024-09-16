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
  configureWebpack: {
    entry: "./src/main.js",
    devServer: {
        hot: true,
    },
    watch: true,
    watchOptions: {
        ignored: /node_modules/,
        poll: 1000,
    },
},
server: {
  host: '0.0.0.0',  // Tüm IP adreslerinden erişime izin ver
  port: 8080,        // Kullanılan portu belirtin
  watch: {
    usePolling: true,  // Dosya değişikliklerini izlemek için polling kullan
  },
},
transpileDependencies: true,

})
