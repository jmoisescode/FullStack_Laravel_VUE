 

import { fileURLToPath, URL } from "node:url";
import vue from '@vitejs/plugin-vue'
import { defineConfig,loadEnv } from "vite"; 
import vueDevTools from "vite-plugin-vue-devtools";
import tailwindcss from "@tailwindcss/vite";
import Components from "unplugin-vue-components/vite"; 
import { PrimeVueResolver } from "@primevue/auto-import-resolver"; 

const proxyPaths = {
  '/api' : '/api',
  // '/auth' : '/auth',  
  //Add another endpoint here
}


export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd());
  process.env = { ...process.env, ...env };

  const proxy = Object.fromEntries(
    Object.entries(proxyPaths).map(([path, endpoint]) => [
      path,
      {
        target: `${env.VITE_APP_ROOTAPI}${endpoint}`,
        changeOrigin: true,  
        rewrite: (urlPath) => urlPath.replace(new RegExp(`^${path}`), ''),
      },
    ]),
  )

  return {
    plugins: [
      tailwindcss(),
      vue(),
      vueDevTools(),
      Components({ 
        resolvers: [PrimeVueResolver()],
      }),
    ],
    theme: {
      extend: {
        fontFamily: {
          'sans': ['Montserrat', 'sans-serif'],
        },
      },
    },
    base: command === 'serve' ? '' : '/',
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
      },
    },
    server : {
      proxy: proxy
    },
    
    build: {
      chunkSizeWarningLimit: 5000
     }
  };
});