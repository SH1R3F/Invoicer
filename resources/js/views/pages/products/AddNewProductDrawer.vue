<script setup>
import { useProductListStore } from '@/views/pages/products/useProductListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  requiredValidator,
} from '@validators'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'productAdded',
])

const productListStore = useProductListStore()

const isFormValid = ref(false)
const refForm = ref()
const sku = ref('')
const name = ref('')
const description = ref('')
const category_id = ref('')
const price = ref('')

const errors = ref({
  sku: undefined,
  name: undefined,
  description: undefined,
  category_id: undefined,
  price: undefined,
})

// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = async () => {
  const { valid } = await refForm.value?.validate();

  if (valid) {
    try {

      const response = await productListStore.addProduct({
        sku: sku.value,
        name: name.value,
        description: name.value,
        name: name.value,
      });

      const { message } = response.data;

      useSiteStore().alert(message);

      emit('productAdded');
      emit('update:isDrawerOpen', false);

      await nextTick(() => {
        refForm.value?.reset();
        refForm.value?.resetValidation();
      });
    } catch (error) {
      const { status, data } = error.response;

      if (status === 422) {
        errors.value = data.errors;
      }
    }
  }
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
};
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <AppDrawerHeaderSection
      :title="$t('Add Product')"
      @cancel="closeNavigationDrawer"
    />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Full name -->
              <VCol cols="12">
                <AppTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  :label="$t('Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  {{ $t('Submit') }}
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  {{ $t('Cancel') }}
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
