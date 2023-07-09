<script setup>
import { useSiteStore } from '@/views/useSiteStore'
import ScrollToTop from '@core/components/ScrollToTop.vue'
import { useThemeConfig } from '@core/composable/useThemeConfig'
import { hexToRgb } from '@layouts/utils'
import { themeConfig } from '@themeConfig'
import { storeToRefs } from 'pinia'
import { useI18n } from 'vue-i18n'
import { useTheme } from 'vuetify'
import RequestLoader from './layouts/components/RequestLoader.vue'

const {
  syncInitialLoaderTheme,
  syncVuetifyThemeWithTheme: syncConfigThemeWithVuetifyTheme,
  isAppRtl,
  handleSkinChanges,
} = useThemeConfig()

const { global } = useTheme()

// ℹ️ Sync current theme with initial loader theme
syncInitialLoaderTheme()
syncConfigThemeWithVuetifyTheme()
handleSkinChanges()

const siteStore = useSiteStore()

const route = useRoute()
const { t } = useI18n()

const updateTitle = () => {
  document.title = route.meta.title? t(route.meta.title) + ' | ' + t(themeConfig.app.title) : t(themeConfig.app.title)
}

watch(route, updateTitle)

const { loading } = storeToRefs(siteStore);
</script>

<template>
  <VLocaleProvider :rtl="isAppRtl">
    <VProgressLinear
      v-if="loading"
      height="2"
      indeterminate
      color="primary"
    />
    <!-- ℹ️ This is required to set the background color of active nav link based on currently active global theme's primary -->
    <VApp :style="`--v-global-theme-primary: ${hexToRgb(global.current.value.colors.primary)}; padding-top: 20px;`">
      <VSnackbar
        v-model="siteStore.alertStatus"
        location="top end"
        variant="flat"
        :color="siteStore.alertColor"
      >
        {{ siteStore.alertMessage }}
      </VSnackbar>


      <RouterView />
      <ScrollToTop />
    </VApp>
  </VLocaleProvider>
</template>
