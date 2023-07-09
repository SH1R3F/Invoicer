<script setup>
import { useCategoryListStore } from '@/views/pages/categories/useCategoryListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  requiredValidator,
} from '@validators'

const categoryListStore = useCategoryListStore()
const route = useRoute()
const categoryData = ref()
const siteStore = useSiteStore()
const refVForm = ref()
const submitting = ref(false)

const errors = ref({
  name: undefined,
})

try {
  const response = await categoryListStore.fetchCategory(Number(route.params.id));

  categoryData.value = response.data

} catch (error) {
  console.error(error);
}
const categoryDataLocal = ref(JSON.parse(JSON.stringify(categoryData.value)))


const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {
      const response = await categoryListStore.updateCategory(categoryData.value.id, {
        name: categoryDataLocal.value.name,
      });

      const { message } = response.data

      siteStore.alert(message)
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
      <!-- 👉 Edit category -->
      <VCard :title="$t('Edit category')">
        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="categoryDataLocal.name"
                  :rules="[requiredValidator]"
                  :label="$t('Name')"
                  :error-messages="errors.name"
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
  action: Update
  subject: category
  title: Edit category
</route>
