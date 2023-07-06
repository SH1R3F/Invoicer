<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'
import { useAppAbility } from '@/plugins/casl/useAppAbility'
import { initialAbility } from '@/plugins/casl/ability'
import {
  requiredValidator,
  emailValidator,
} from '@validators'
import { useI18n } from 'vue-i18n'

const ability = useAppAbility()
const userListStore = useUserListStore()
const siteStore = useSiteStore()
const router = useRouter()
const refVForm = ref()
const submitting = ref(false)


const response = await userListStore.fetchMe();

const { userData } = response.data;

const errors = ref({
  name: undefined,
  email: undefined,
})

const refInputEl = ref()
const isConfirmDialogOpen = ref(false)
const userDataLocal = ref(structuredClone(userData))
const isAccountDeactivated = ref(false)
const validateAccountDeactivation = [v => !!v || useI18n().t('Please confirm account deactivation')]

const resetForm = () => {
  userDataLocal.value = structuredClone(userData)
}

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target

  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
        userDataLocal.value.image = fileReader.result
        userDataLocal.value.avatar = files[0]
      }
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  userDataLocal.value.image = userData.image
  userDataLocal.value.avatar = userData.avatar
};

const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {

      var formData = new FormData();

      formData.append("name", userDataLocal.value.name);
      formData.append("email", userDataLocal.value.email);
      formData.append("avatar", userDataLocal.value.avatar);

      formData.append("_method", 'PUT');

      const response = await userListStore.updateMe(formData);

      const { message, userData } = response.data

      localStorage.setItem('userData', JSON.stringify(userData))
      userListStore.userData = userData;

      siteStore.alert(message)
    } catch (error) {
      const { message, errors: errs } = error.response.data

      errors.value = errs;
      siteStore.alert(message, 'error')
    }
  }
  submitting.value = false;
}

const deactivateAccount = async state => {
  if (state) {
    try {
      await userListStore.deactivateMe();

      localStorage.removeItem('userData');
      localStorage.removeItem('accessToken');
      localStorage.removeItem('userAbilities');
      ability.update(initialAbility);

      return router.push('/login');
    } catch (error) {
      console.log(error)
    }
  }
};
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard :title="$t('Profile Details')">
        <VCardText class="d-flex">
          <!-- 👉 Avatar -->
          <VAvatar
            rounded
            size="100"
            class="me-6"
            :image="userDataLocal.image"
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
              {{ $t('Allowed JPG, GIF or PNG. Max size of 2M') }}
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText class="pt-2">
          <!-- 👉 Form -->
          <VForm
            ref="refVForm"
            class="mt-6"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Full Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="userDataLocal.name"
                  :label="$t('Full Name')"
                  :rules="[requiredValidator]"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="userDataLocal.email"
                  :label="$t('Email')"
                  type="email"
                  :rules="[emailValidator]"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- 👉 Form Actions -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn :disabled="submitting">
                  {{ $t('Save changes') }}
                </VBtn>

                <VBtn
                  color="secondary"
                  variant="tonal"
                  type="reset"
                  @click.prevent="resetForm"
                >
                  {{ $t('Reset') }}
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Delete Account -->
      <VCard :title="$t('Delete Account')">
        <VCardText>
          <!-- 👉 Checkbox and Button  -->
          <div>
            <VCheckbox
              v-model="isAccountDeactivated"
              :rules="validateAccountDeactivation"
              :label="$t('I confirm my account deactivation')"
            />
          </div>

          <VBtn
            :disabled="!isAccountDeactivated"
            color="error"
            class="mt-3"
            @click="isConfirmDialogOpen = true"
          >
            {{ $t('Deactivate Account') }}
          </VBtn>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <!-- Confirm Dialog -->
  <ConfirmDialog
    v-model:isDialogVisible="isConfirmDialogOpen"
    :confirmation-question="$t('Are you sure you want to deactivate your account?')"
    :confirm-title="$t('Deactivated!')"
    :confirm-msg="$t('Your account has been deactivated successfully.')"
    :cancel-title="$t('Cancelled')"
    :cancel-msg="$t('Account Deactivation Cancelled!')"
    @confirm="deactivateAccount"
  />
</template>
