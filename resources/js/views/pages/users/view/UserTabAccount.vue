<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const props = defineProps({
  userData: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'userUpdated',
])

const roles = ref([])
const userListStore = useUserListStore()

userListStore.fetchRoles().then(response => {
  roles.value = response.data
})

const form = ref({ ...props.userData })

const errors = ref({
  name: undefined,
  email: undefined,
  role_id: undefined,
  password: undefined,
})

const isNewPasswordVisible = ref(false)

const updateUser = () => {
  userListStore.updateUser(props.userData.id, form.value).then(response => {
    const { status, message, user } = response.data
    if (status == 'success') {
      emit('userUpdated', user)
      useSiteStore().alert(message)
    } 
  }).catch (error => {
    const { status, data } = error.response
    if (status == 422) {
      errors.value = data.errors
    }
  })
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Personal information -->
      <VCard :title="$t('Personal information')">
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

          <VForm @submit.prevent="updateUser">
            <VRow>
              <!-- 👉 Name -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.name"
                  :rules="[requiredValidator]"
                  :label="$t('Full Name')"
                  :error-messages="errors.name"
                />
              </VCol>
      
              <!-- 👉 Email -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.email"
                  :rules="[requiredValidator, emailValidator]"
                  :label="$t('Email')"
                  :error-messages="errors.email"
                />
              </VCol>
              
              <!-- 👉 Role -->
              <VCol cols="12">
                <AppSelect
                  v-model="form.role_id"
                  :label="$t('Select Role')"
                  :rules="[requiredValidator]"
                  :items="roles"
                  item-title="role"
                  item-value="id"
                  :error-messages="errors.role_id"
                />
              </VCol>

              <!-- 👉 Password -->
              <VCol cols="12">
                <AppTextField
                  v-model="form.password"
                  :label="$t('Password')"
                  :error-messages="errors.password"
                  :type="isNewPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isNewPasswordVisible ? 'tabler-eye-off' : 'tabler-eye'"
                  @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                />
              </VCol>

              <VCol cols="12">
                <VBtn type="submit">
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
