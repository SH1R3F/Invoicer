<script setup>
import { useProductListStore } from '@/views/pages/products/useProductListStore'
import { useSiteStore } from '@/views/useSiteStore'
import { avatarText } from '@core/utils/formatters'
import { paginationMeta } from '@layouts/utils'
import { useI18n } from 'vue-i18n'
import { VDataTableServer } from 'vuetify/labs/VDataTable'

const productListStore = useProductListStore()
const searchQuery = ref('')
const categories = ref([])
const selectedCategory = ref()
const totalPage = ref(1)
const totalProducts = ref(0)
const products = ref([])
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
    title: t('Description'),
    key: 'description',
  },
  {
    title: t('Category'),
    key: 'category',
  },
  {
    title: t('Price'),
    key: 'price',
  },
  {
    title: t('Actions'),
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching products
const fetchProducts = async () => {
  loading.value = true;
  try {
    const response = await productListStore.fetchProducts({
      q: searchQuery.value,
      options: options.value,
      category_id: selectedCategory.value,
    });

    const { data, meta, categories: categoriesData } = response.data;

    products.value = data;
    totalPage.value = meta.last_page;
    totalProducts.value = meta.total;
    options.value.page = meta.current_page;
    categories.value = categoriesData;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false
  }
};

watchEffect(await fetchProducts)

const deleteProduct = async id => {
  try {
    const response = await productListStore.deleteProduct(id);
    const { message } = response.data;

    useSiteStore().alert(message);

    // refetch Products
    await fetchProducts();
  } catch (error) {
    console.error(error);
  }
}

const exportProducts = async () => {
  try {
    const response = await productListStore.exportProducts({
      q: searchQuery.value,
      category_id: selectedCategory.value,
      options: options.value,
    });

    const { url } = response.data

    window.location = url
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
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
              <!-- 👉 Select Category -->
              <VCol cols="12">
                <AppSelect
                  v-model="selectedCategory"
                  :label="$t('Select Category')"
                  :items="categories"
                  item-title="name"
                  item-value="id"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

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

            <div class="app-product-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="inline-size: 10rem;">
                <AppTextField
                  v-model="searchQuery"
                  :placeholder="$t('Search')"
                  density="compact"
                />
              </div>

              <!-- 👉 Export button -->
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
                @click.prevent="exportProducts"
              >
                {{ $t('Export') }}
              </VBtn>

              <!-- 👉 Add product button -->
              <VBtn
                prepend-icon="tabler-plus"
                :to="{name: 'products-create'}"
              >
                {{ $t('Add New Product') }}
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="products"
            :items-length="totalProducts"
            :headers="headers"
            :loading="loading"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- Product -->
            <template #item.name="{ item }">
              <div class="d-flex align-center">
                <VAvatar
                  size="34"
                  class="me-3"
                  variant="tonal"
                >
                  <VImg
                    v-if="item.raw.image"
                    :src="item.raw.image"
                  />
                  <span v-else>{{ avatarText(item.raw.name) }}</span>
                </VAvatar>

                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      :to="{ name: 'products-id', params: { id: item.raw.id } }"
                      class="font-weight-medium product-list-name"
                    >
                      {{ item.raw.name }}
                    </RouterLink>
                  </h6>
                  <span class="text-sm text-medium-emphasis">#{{ item.raw.sku }}</span>
                </div>
              </div>
            </template>

            <!-- Description -->
            <template #item.description="{ item }">
              <div class="ellipsis-wrapper">
                {{ item.raw.description }}
              </div>
            </template>

            <!-- Category -->
            <template #item.category="{ item }">
              <div class="d-flex align-center">
                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      v-if="item.raw.category"
                      :to="{ name: 'categories-id', params: { id: item.raw.category?.id } }"
                      class="font-weight-medium product-list-name"
                    >
                      {{ item.raw.category.name }}
                    </RouterLink>
                    <span
                      v-else
                      class="font-weight-medium product-list-name"
                    >
                      {{ $t('No category') }}
                    </span>
                  </h6>
                </div>
              </div>
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <IconBtn
                v-if="item.raw.editable"
                :to="{ name: 'products-id', params: { id: item.raw.id } }"
              >
                <VIcon icon="tabler-edit" />
              </IconBtn>

              <IconBtn
                v-if="item.raw.deletable"
                @click="deleteProduct(item.raw.id)"
              >
                <VIcon icon="tabler-trash" />
              </IconBtn>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalProducts) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalProducts / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : Math.ceil(totalProducts / options.itemsPerPage)"
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
      </vcol>
    </vrow>
  </section>
</template>

<style lang="scss">
.app-product-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.product-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
meta:
  action: Read
  subject: product
  title: Products
</route>
