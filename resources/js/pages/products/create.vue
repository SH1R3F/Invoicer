<script setup>
import { useProductListStore } from '@/views/pages/products/useProductListStore'
import { useCategoryListStore } from '@/views/pages/categories/useCategoryListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  requiredValidator,
} from '@validators'

const categoryListStore = useCategoryListStore()
const productListStore = useProductListStore()
const router = useRouter()

const productData = ref({
  sku: '',
  name: '',
  description: '',
  image: null,
  category_id: '',
  price: '',
})

const categories = ref([])
const siteStore = useSiteStore()
const refVForm = ref()
const submitting = ref(false)
const preview = ref()
const refInputEl = ref()

const errors = ref({
  sku: undefined,
  name: undefined,
  description: undefined,
  image: undefined,
  category_id: undefined,
  price: undefined,
})

try {
  const response = await categoryListStore.fetchCategories();

  categories.value = response.data.data
} catch (error) {
  console.error(error);
}
const productDataLocal = ref(JSON.parse(JSON.stringify(productData.value)))

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target

  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
        preview.value = fileReader.result
        productDataLocal.value.image = files[0]
      }
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  preview.value = props.userData.image
  productDataLocal.value.avatar = props.userData.avatar
};


const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {

      var formData = new FormData();
      formData.append("sku", productDataLocal.value.sku);
      formData.append("name", productDataLocal.value.name);
      formData.append("description", productDataLocal.value.description);
      formData.append("image", productDataLocal.value.image);
      formData.append("category_id", productDataLocal.value.category_id);
      formData.append("price", productDataLocal.value.price);

      const response = await productListStore.addProduct(formData);

      const { message } = response.data

      // redirect
      router.push('/products').then(() => {
        useSiteStore().alert(message)
      })
    } catch (error) {
      const { status, data } = error.response
      if (status == 422) {
        const { message, errors: errs } = data

        errors.value = errs;
        siteStore.alert(message, 'error')
      }

    }
  }
  submitting.value = false;
};
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Edit product -->
      <VCard :title="$t('Edit product')">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded
            size="100"
            class="me-6"
            :image="preview"
          />

          <!-- 👉 Upload Photo -->
          <form class="d-flex flex-column justify-center gap-4">
            <div class="d-flex flex-wrap gap-2">
              <VBtn
                color="primary"
                @click="refInputEl?.click()"
              >
                <VIcon
                  icon="tabler-cloud-upload"
                  class="d-sm-none"
                />
                <span class="d-none d-sm-block">{{ $t('Upload new photo') }}</span>
              </VBtn>

              <input
                ref="refInputEl"
                type="file"
                name="file"
                accept=".jpeg,.png,.jpg,GIF"
                hidden
                @input="changeAvatar"
              >

              <VBtn
                type="reset"
                color="secondary"
                variant="tonal"
                @click="resetAvatar"
              >
                <span class="d-none d-sm-block">{{ $t('Reset') }}</span>
                <VIcon
                  icon="tabler-refresh"
                  class="d-sm-none"
                />
              </VBtn>
            </div>

            <p class="text-body-1 mb-0">
              {{ $t('Allowed JPG, GIF or PNG. Max size of 10M') }}
            </p>
          </form>
        </VCardText>

        <VDivider />
        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Name -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="productDataLocal.name"
                  :rules="[requiredValidator]"
                  :label="$t('Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Sku -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="productDataLocal.sku"
                  :rules="[requiredValidator]"
                  :label="$t('Sku')"
                  :error-messages="errors.sku"
                  :hint="$t('Leave empty for auto generate')"
                  persistent-hint
                />
              </VCol>

              <!-- 👉 Category -->
              <VCol
                cols="12"
                md="6"
              >
                <AppSelect
                  v-model="productDataLocal.category_id"
                  :label="$t('Select Category')"
                  :error-messages="errors.category_id"
                  :items="categories"
                  item-title="name"
                  item-value="id"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>

              <!-- 👉 Price -->
              <VCol
                cols="12"
                md="6"
              >
                <AppTextField
                  v-model="productDataLocal.price"
                  :rules="[requiredValidator]"
                  :label="$t('Price')"
                  prefix="$"
                  type="number"
                  :error-messages="errors.price"
                  :hint="$t('Price without taxes')"
                  persistent-hint
                />
              </VCol>

              <!-- 👉 Description -->
              <VCol cols="12">
                <AppTextarea
                  v-model="productDataLocal.description"
                  :rules="[requiredValidator]"
                  :label="$t('Description')"
                  auto-grow
                  rows="3"
                  row-height="25"
                  :error-messages="errors.description"
                />
              </VCol>

              <VCol cols="12">
                <VBtn
                  type="submit"
                  :disabled="submitting"
                >
                  {{ $t('Submit') }}
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
meta:
  action: Create
  subject: product
  title: Create product
</route>
