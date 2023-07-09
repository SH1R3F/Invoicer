<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  emailValidator,
  requiredValidator,
  lengthValidator,
  regexValidator,
} from '@validators'

const props = defineProps({
  userData: {
    type: Object,
    required: true,
  },
  roles: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'userUpdated',
])

const userListStore = useUserListStore()
const siteStore = useSiteStore()

const errors = ref({
  name: undefined,
  email: undefined,
  role: undefined,
  password: undefined,
})

const refInputEl = ref()
const userDataLocal = ref(JSON.parse(JSON.stringify(props.userData)))
const isNewPasswordVisible = ref(false)
const submitting = ref(false)
const refVForm = ref()
const router = useRouter()

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
  userDataLocal.value.image = props.userData.image
  userDataLocal.value.avatar = props.userData.avatar
};

const onSubmit = async () => {
  submitting.value = true;

  const { valid } = await refVForm.value?.validate();
  if (valid) {
    try {

      var formData = new FormData();
      formData.append("name", userDataLocal.value.name);
      formData.append("email", userDataLocal.value.email);
      formData.append("role", userDataLocal.value.role);
      formData.append("avatar", userDataLocal.value.avatar);
      formData.append("_method", 'PUT');

      const response = await userListStore.updateUser(props.userData.id, formData);
      const { message, user } = response.data

      emit('userUpdated', user)
      if (!user.editable) {
        router.push('/users');
      }

      userDataLocal.value.password = '';

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
      <!-- 👉 Personal information -->
      <VCard :title="$t('Personal information')">
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
              {{ $t('Allowed JPG, GIF or PNG. Max size of 10M') }}
            </p>
          </form>
        </VCardText>

        <VDivider />

        <VCardText>
          <VAlert
            variant="tonal"
            color="warning"
            class="mb-4"
          >
            <VAlertTitle class="mb-1 text-subtitle-2">
              {{ $t("Leave password empty in case you don't want to change it") }}
            </VAlertTitle>
          </VAlert>

          <VForm
            ref="refVForm"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="userDataLocal.name"
                  :rules="[requiredValidator]"
                  :label="$t('Full Name')"
                  :error-messages="errors.name"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="userDataLocal.email"
                  :rules="[requiredValidator, emailValidator]"
                  :label="$t('Email')"
                  :error-messages="errors.email"
                />
              </VCol>

              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="userDataLocal.role"
                  :label="$t('Select Role')"
                  :rules="[requiredValidator]"
                  :items="roles"
                  :error-messages="errors.role"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="userDataLocal.password"
                  :label="$t('Password')"
                  :rules="[lengthValidator(userDataLocal.password, 8), regexValidator(userDataLocal.password, /^(?=.*[a-z])(?=.*[0-9\s\W])/i)]"
                  :error-messages="errors.password"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
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
