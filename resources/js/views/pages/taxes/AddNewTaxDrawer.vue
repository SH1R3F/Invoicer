<script setup>
import { useTaxListStore } from '@/views/pages/taxes/useTaxListStore'
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
  'taxAdded',
])

const taxListStore = useTaxListStore()

const isFormValid = ref(false)
const refForm = ref()
const name = ref('')
const value = ref(0)
const type = ref('percentage')
const defaulted = ref('')

const errors = ref({
  name: undefined,
  value: undefined,
  type: undefined,
  default: undefined,
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
      const response = await taxListStore.addTax({
        name: name.value,
        value: value.value,
        type: type.value,
        default: defaulted.value,
      });

      const { message } = response.data;

      useSiteStore().alert(message);

      emit('taxAdded');
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
      :title="$t('Add Tax')"
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
              <!-- 👉 Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  :label="$t('Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Type -->
              <VCol cols="12">
                <AppSelect
                  v-model="type"
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
                  v-model="value"
                  :label="$t('Value')"
                  :prefix="type == 'percentage' ? '%' : '$'"
                  type="number"
                  :rules="[requiredValidator]"
                  :error-messages="errors.value"
                />
              </VCol>

              <!-- 👉 Default -->
              <VCol cols="12">
                <VCheckbox
                  v-model="defaulted"
                  :label="$t('Default')"
                  :error-messages="errors.default"
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
