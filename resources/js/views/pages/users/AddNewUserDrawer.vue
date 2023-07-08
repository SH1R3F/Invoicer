<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  emailValidator,
  requiredValidator,
  lengthValidator,
  regexValidator,
} from '@validators'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  roles: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'userAdded',
])

const userListStore = useUserListStore()

const isFormValid = ref(false)
const refForm = ref()
const name = ref('')
const email = ref('')
const role = ref('')
const password = ref('')
const isPasswordVisible = ref(false)

const errors = ref({
  name: undefined,
  email: undefined,
  role: undefined,
  password: undefined,
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
      const response = await userListStore.addUser({
        name: name.value,
        role: role.value,
        email: email.value,
        password: password.value,
      });

      const { message } = response.data;

      useSiteStore().alert(message);

      emit('userAdded');
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
      :title="$t('Add User')"
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
                  :label="$t('Full Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  :label="$t('Email')"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="role"
                  :label="$t('Select Role')"
                  :rules="[requiredValidator]"
                  :items="roles"
                  :error-messages="errors.role"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="password"
                  :label="$t('Password')"
                  :rules="[requiredValidator, lengthValidator(password, 8), regexValidator(password, /^(?=.*[a-z])(?=.*[0-9\s\W])/i)]"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :error-messages="errors.password"
                  :append-inner-icon="isPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
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
