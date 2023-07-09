<script setup>
import AddNewCategoryDrawer from '@/views/pages/categories/AddNewCategoryDrawer.vue'
import { useCategoryListStore } from '@/views/pages/categories/useCategoryListStore'
import { useSiteStore } from '@/views/useSiteStore'
import { avatarText } from '@core/utils/formatters'
import { paginationMeta } from '@layouts/utils'
import { useI18n } from 'vue-i18n'
import { VDataTableServer } from 'vuetify/labs/VDataTable'

const categoryListStore = useCategoryListStore()
const searchQuery = ref('')
const selectedRole = ref()
const totalPage = ref(1)
const totalCategories = ref(0)
const categories = ref([])
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

  // TODO
  // {
  //   title: t('Products'),
  //   key: 'products',
  // },
  {
    title: t('Actions'),
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching categories
const fetchCategories = async () => {
  loading.value = true;
  try {
    const response = await categoryListStore.fetchCategories({
      q: searchQuery.value,
      options: options.value,
    });

    const { data, meta } = response.data;

    categories.value = data;
    totalPage.value = meta.last_page;
    totalCategories.value = meta.total;
    options.value.page = meta.current_page;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false
  }
};

watchEffect(await fetchCategories)

const isAddNewCategoryDrawerVisible = ref(false)

const deleteCategory = async id => {
  try {
    const response = await categoryListStore.deleteCategory(id);
    const { message } = response.data;

    useSiteStore().alert(message);

    // refetch Categories
    await fetchCategories();
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

            <div class="app-category-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="inline-size: 10rem;">
                <AppTextField
                  v-model="searchQuery"
                  :placeholder="$t('Search')"
                  density="compact"
                />
              </div>

              <!-- 👉 Add category button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewCategoryDrawerVisible = true"
              >
                {{ $t('Add New Category') }}
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="categories"
            :items-length="totalCategories"
            :headers="headers"
            :loading="loading"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- Category -->
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
                      :to="{ name: 'categories-id', params: { id: item.raw.id } }"
                      class="font-weight-medium category-list-name"
                    >
                      {{ item.raw.name }}
                    </RouterLink>
                  </h6>
                </div>
              </div>
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <IconBtn
                v-if="item.raw.editable"
                :to="{ name: 'categories-id', params: { id: item.raw.id } }"
              >
                <VIcon icon="tabler-edit" />
              </IconBtn>

              <IconBtn
                v-if="item.raw.deletable"
                @click="deleteCategory(item.raw.id)"
              >
                <VIcon icon="tabler-trash" />
              </IconBtn>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalCategories) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalCategories / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : Math.ceil(totalCategories / options.itemsPerPage)"
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

        <!-- 👉 Add New Category -->
        <AddNewCategoryDrawer
          v-model:isDrawerOpen="isAddNewCategoryDrawerVisible"
          @category-added="fetchCategories"
        />
      </vcol>
    </vrow>
  </section>
</template>

<style lang="scss">
.app-category-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.category-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
meta:
  action: Read
  subject: category
  title: Categories
</route>
