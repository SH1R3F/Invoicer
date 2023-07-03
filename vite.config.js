import { defineConfig } from "vite"
import laravel from "laravel-vite-plugin"
import vue from "@vitejs/plugin-vue"
import { fileURLToPath } from "node:url"
import vuetify from "vite-plugin-vuetify"
import vueJsx from "@vitejs/plugin-vue-jsx"
import Pages from "vite-plugin-pages"
import Layouts from "vite-plugin-vue-layouts"
import Components from "unplugin-vue-components/vite"
import AutoImport from "unplugin-auto-import/vite"

import DefineOptions from "unplugin-vue-define-options/vite"

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    vue(),
    vueJsx(),

    // https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vite-plugin
    vuetify({
      styles: {
        configFile: "resources/js/styles/variables/_vuetify.scss",
      },
    }),

    Pages({
      dirs: ["./resources/js/pages"],
    }),
    Layouts({
      layoutsDirs: "./resources/js/layouts/",
    }),
    Components({
      dirs: [
        "resources/js/@core/components",
        "resources/js/views/demos",
        "resources/js/components",
      ],
      dts: true,
    }),
    AutoImport({
      eslintrc: {
        enabled: true,
        filepath: "./.eslintrc-auto-import.json",
      },
      imports: ["vue", "vue-router", "@vueuse/core", "@vueuse/math", "pinia"],
      vueTemplate: true,
    }),
    DefineOptions(),
  ],
  define: {
    "process.env": {
      baseUrl: "https://invoicer.test/api/v1/",
    },
  },

  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./resources/js", import.meta.url)),
      "@themeConfig": fileURLToPath(
        new URL("./themeConfig.js", import.meta.url),
      ),
      "@core": fileURLToPath(new URL("./resources/js/@core", import.meta.url)),
      "@layouts": fileURLToPath(
        new URL("./resources/js/@layouts", import.meta.url),
      ),
      "@images": fileURLToPath(
        new URL("./resources/js/assets/images/", import.meta.url),
      ),
      "@styles": fileURLToPath(
        new URL("./resources/js/styles/", import.meta.url),
      ),
      "@configured-variables": fileURLToPath(
        new URL(
          "./resources/js/styles/variables/_template.scss",
          import.meta.url,
        ),
      ),
      "@axios": fileURLToPath(
        new URL("./resources/js/plugins/axios", import.meta.url),
      ),
      "@validators": fileURLToPath(
        new URL("./resources/js/@core/utils/validators", import.meta.url),
      ),
      apexcharts: fileURLToPath(
        new URL("node_modules/apexcharts-clevision", import.meta.url),
      ),
    },
  },
  build: {
    chunkSizeWarningLimit: 5000,
  },
  optimizeDeps: {
    exclude: ["vuetify"],
    entries: ["./resources/js/**/*.vue"],
  },
})
