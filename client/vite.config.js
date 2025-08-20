import { fileURLToPath, URL } from 'node:url'

import { defineConfig, loadEnv  } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import Components from 'unplugin-vue-components/vite'
import MotionResolver from 'motion-v/resolver'


// https://vite.dev/config/
export default defineConfig(({ mode }) => {

  const env = loadEnv(mode, process.cwd(), '')

  return{
    plugins: [
      vue(),
      vueDevTools(),
      Components({
        dts: true,
        resolvers: [
          MotionResolver()
        ],
      }),
    ],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url))
      },
    },
    server: {
      host: env.VITE_HOST,
      port: parseInt(env.VITE_PORT),
    },
  }
})
