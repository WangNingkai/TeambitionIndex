import vue from '@vitejs/plugin-vue'
/**
 * @type {import('vite').UserConfig}
 */
export default {
  plugins: [vue()],
  optimizeDeps: {
    include: ['clipboard/src/clipboard.js'],
  },
  build: {
    rollupOptions: {
      output: {
        compact: true,
        manualChunks: {
          vendor: ['store', 'axios', 'js-cookie', 'default-passive-events', 'plyr', 'clipboard'],
        },
      },
    },
  },
}
