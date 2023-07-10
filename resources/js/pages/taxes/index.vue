<script setup>
import AddNewTaxDrawer from '@/views/pages/taxes/AddNewTaxDrawer.vue'
import { useTaxListStore } from '@/views/pages/taxes/useTaxListStore'
import { useSiteStore } from '@/views/useSiteStore'
import { avatarText } from '@core/utils/formatters'
import { paginationMeta } from '@layouts/utils'
import { useI18n } from 'vue-i18n'
import { VDataTableServer } from 'vuetify/labs/VDataTable'

const taxListStore = useTaxListStore()
const searchQuery = ref('')
const totalPage = ref(1)
const totalTaxes = ref(0)
const taxes = ref([])
const { t } = useI18n()
const loading = ref(false)

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

// Headers
const headers = [
  {
    title: t('Name'),
    key: 'name',
  },
  {
    title: t('Tax'),
    key: 'tax',
  },
  {
    title: t('Default'),
    key: 'default',
  },
  {
    title: t('Actions'),
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching taxes
const fetchTaxes = async () => {
  loading.value = true;
  try {
    const response = await taxListStore.fetchTaxes({
      q: searchQuery.value,
      options: options.value,
    });

    const { data, meta } = response.data;

    taxes.value = data;
    totalPage.value = meta.last_page;
    totalTaxes.value = meta.total;
    options.value.page = meta.current_page;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false
  }
};

watchEffect(await fetchTaxes)

const isAddNewTaxDrawerVisible = ref(false)

const deleteTax = async id => {
  try {
    const response = await taxListStore.deleteTax(id);
    const { message } = response.data;

    useSiteStore().alert(message);

    // refetch Taxes
    await fetchTaxes();
  } catch (error) {
    console.error(error);
  }
};
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard :title="$t('Search Filter')">
          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div class="me-3 d-flex gap-3">
              <AppSelect
                :model-value="options.itemsPerPage"
                :items="[
                  { value: 10, title: '10' },
                  { value: 25, title: '25' },
                  { value: 50, title: '50' },
                  { value: 100, title: '100' },
                  { value: -1, title: 'All' },
                ]"
                style="width: 6.25rem;"
                @update:model-value="options.itemsPerPage = parseInt($event, 10)"
              />
            </div>
            <VSpacer />

            <div class="app-tax-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="inline-size: 10rem;">
                <AppTextField
                  v-model="searchQuery"
                  :placeholder="$t('Search')"
                  density="compact"
                />
              </div>

              <!-- 👉 Add tax button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewTaxDrawerVisible = true"
              >
                {{ $t('Add New Tax') }}
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="taxes"
            :items-length="totalTaxes"
            :headers="headers"
            :loading="loading"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- Tax name -->
            <template #item.name="{ item }">
              <div class="d-flex align-center">
                <VAvatar
                  size="34"
                  class="me-3"
                  variant="tonal"
                >
                  <span>{{ avatarText(item.raw.name) }}</span>
                </VAvatar>

                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      :to="{ name: 'taxes-id', params: { id: item.raw.id } }"
                      class="font-weight-medium tax-list-name"
                    >
                      {{ item.raw.name }}
                    </RouterLink>
                  </h6>
                </div>
              </div>
            </template>

            <!-- Default tax -->
            <template #item.default="{ item }">
              <VChip
                :color="item.raw.default ? 'success' : ''"
                class="text-base"
              >
                {{ item.raw.default ? $t('Default') : $t('Not default') }}
              </VChip>
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <IconBtn
                v-if="item.raw.editable"
                :to="{ name: 'taxes-id', params: { id: item.raw.id } }"
              >
                <VIcon icon="tabler-edit" />
              </IconBtn>

              <IconBtn
                v-if="item.raw.deletable"
                @click="deleteTax(item.raw.id)"
              >
                <VIcon icon="tabler-trash" />
              </IconBtn>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalTaxes) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalTaxes / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : Math.ceil(totalTaxes / options.itemsPerPage)"
                >
                  <template #prev="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      {{ $t('Previous') }}
                    </VBtn>
                  </template>

                  <template #next="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      {{ $t('Next') }}
                    </VBtn>
                  </template>
                </VPagination>
              </div>
            </template>
          </VDataTableServer>
          <!-- SECTION -->
        </VCard>

        <!-- 👉 Add New Tax -->
        <AddNewTaxDrawer
          v-model:isDrawerOpen="isAddNewTaxDrawerVisible"
          @tax-added="fetchTaxes"
        />
      </vcol>
    </vrow>
  </section>
</template>

<style lang="scss">
.app-tax-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.tax-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
meta:
  action: Read
  subject: tax
  title: Taxes
</route>
