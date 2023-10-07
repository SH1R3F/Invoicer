<script setup>
import { useTaxListStore } from '@/views/pages/taxes/useTaxListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  requiredValidator,
} from '@validators'

const taxListStore = useTaxListStore()
const route = useRoute()
const taxData = ref()
const siteStore = useSiteStore()
const refVForm = ref()
const submitting = ref(false)

const errors = ref({
  name: undefined,
  type: undefined,
  value: undefined,
  default: undefined,
})

try {
  const response = await taxListStore.fetchTax(Number(route.params.id));

  taxData.value = response.data

} catch (error) {
  console.error(error);
}
const taxDataLocal = ref(JSON.parse(JSON.stringify(taxData.value)))


const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {
      const response = await taxListStore.updateTax(taxData.value.id, {
        name: taxDataLocal.value.name,
        type: taxDataLocal.value.type,
        value: taxDataLocal.value.value,
        default: taxDataLocal.value.default,
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
      <!-- 👉 Edit tax -->
      <VCard :title="$t('Edit tax')">
        <VCardText>
          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="taxDataLocal.name"
                  :rules="[requiredValidator]"
                  :label="$t('Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Type -->
              <VCol cols="12">
                <AppSelect
                  v-model="taxDataLocal.type"
                  :items="[{key: $t('Percentage'), value: 'percentage'},{key: $t('Fixed'), value: 'fixed'}]"
                  :label="$t('Type')"
                  :rules="[requiredValidator]"
                  :error-messages="errors.type"
                  item-title="key"
                  item-value="value"
                />
              </VCol>

              <!-- 👉 Value -->
              <VCol cols="12">
                <AppTextField
                  v-model="taxDataLocal.value"
                  :label="$t('Value')"
                  :prefix="taxDataLocal.type == 'percentage' ? '%' : '$'"
                  type="number"
                  :rules="[requiredValidator]"
                  :error-messages="errors.value"
                />
              </VCol>

              <!-- 👉 Default -->
              <VCol cols="12">
                <VCheckbox
                  v-model="taxDataLocal.default"
                  :label="$t('Default')"
                  :error-messages="errors.default"
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
  subject: tax
  title: Edit tax
</route>
