import vue from '@vitejs/plugin-vue'
/**
 * @type {import('vite').UserConfig}
 */
export default {
  plugins: [vue()],
  optimizeDeps: {
    include:['clipboard/src/clipboard.js']
  },
}
