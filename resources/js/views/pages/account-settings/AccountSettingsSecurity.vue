<script setup>
import {
  requiredValidator,
  lengthValidator,
  regexValidator,
  EqualsValidator,
} from '@validators'
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'

const refVForm = ref()
const submitting = ref(false)

const errors = ref({
  current_password: undefined,
  new_password: undefined,
})

const isCurrentPasswordVisible = ref(false)
const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')

const passwordRequirements = [
  'Minimum 8 characters long - the more, the better',
  'At least one number, symbol, or whitespace character',
];

const userListStore = useUserListStore()
const siteStore = useSiteStore()

const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {
      const response = await userListStore.updatePassword({
        current_password: currentPassword.value,
        new_password: newPassword.value,
        new_password_confirmation: confirmPassword.value,
      });

      const { message } = response.data

      currentPassword.value = ''
      newPassword.value = ''
      confirmPassword.value = ''

      siteStore.alert(message)
    } catch (error) {
      const { message, errors: errs } = error.response.data

      errors.value = errs;
      siteStore.alert(message, 'error')
    }
  }
  submitting.value = false;
};
</script>

<template>
  <VRow>
    <!-- SECTION: Change Password -->
    <VCol cols="12">
      <VCard :title="$t('Change Password')">
        <VForm
          ref="refVForm"
          @submit.prevent="onSubmit"
        >
          <VCardText class="pt-0">
            <!-- 👉 Current Password -->
            <VRow>
              <VCol cols="12">
                <!-- 👉 current password -->
                <AppTextField
                  v-model="currentPassword"
                  :type="isCurrentPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isCurrentPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :label="$t('Current Password')"
                  :rules="[requiredValidator]"
                  :error-messages="errors.current_password"
                  @click:append-inner="isCurrentPasswordVisible = !isCurrentPasswordVisible"
                />
              </VCol>
            </VRow>

            <!-- 👉 New Password -->
            <VRow>
              <VCol
                cols="12"
                md="6"
              >
                <!-- 👉 new password -->
                <AppTextField
                  v-model="newPassword"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :label="$t('New Password')"
                  :rules="[requiredValidator, lengthValidator(newPassword, 8), regexValidator(newPassword, /^(?=.*[a-z])(?=.*[0-9\s\W])/i)]"
                  :error-messages="errors.new_password"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                />
              </VCol>

              <VCol
                cols="12"
                md="6"
              >
                <!-- 👉 confirm password -->
                <AppTextField
                  v-model="confirmPassword"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  :label="$t('Confirm New Password')"
                  :rules="[requiredValidator, EqualsValidator(confirmPassword, newPassword)]"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>
            </VRow>
          </VCardText>

          <!-- 👉 Password Requirements -->
          <VCardText>
            <h6 class="text-base font-weight-medium mb-3">
              {{ $t('Password Requirements:') }}
            </h6>

            <VList class="card-list">
              <VListItem
                v-for="item in passwordRequirements"
                :key="item"
                :title="$t(item)"
                class="text-medium-emphasis"
              >
                <template #prepend>
                  <VIcon
                    size="8"
                    icon="tabler-circle"
                    class="me-3"
                  />
                </template>
              </VListItem>
            </VList>
          </VCardText>

          <!-- 👉 Action Buttons -->
          <VCardText class="d-flex flex-wrap gap-4">
            <VBtn
              type="submit"
              :disabled="submitting"
            >
              {{ $t('Save changes') }}
            </VBtn>

            <VBtn
              type="reset"
              color="secondary"
              variant="tonal"
            >
              {{ $t('Reset') }}
            </VBtn>
          </VCardText>
        </VForm>
      </VCard>
    </VCol>
    <!-- !SECTION -->
  </VRow>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 5px;
}

.server-close-btn {
  inset-inline-end: 0.5rem;
}
</style>
