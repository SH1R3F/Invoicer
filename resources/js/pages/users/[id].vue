<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import UserBioPanel from '@/views/pages/users/view/UserBioPanel.vue'
import UserTabAccount from '@/views/pages/users/view/UserTabAccount.vue'

const userListStore = useUserListStore()
const route = useRoute()
const userData = ref()

userListStore.fetchUser(Number(route.params.id)).then(response => {
  userData.value = response.data
})

const updateUser = user => {
  userData.value = user
}
</script>

<template>
  <VRow v-if="userData">
    <VCol
      cols="12"
      md="5"
      lg="4"
    >
      <UserBioPanel :user-data="userData" />
    </VCol>

    <VCol
      cols="12"
      md="7"
      lg="8"
    >
      <UserTabAccount
        :user-data="userData"
        @user-updated="updateUser"
      />
    </VCol>
  </VRow>
</template>

<route lang="yaml">
meta:
  action: Update
  subject: user
  title: Edit user
</route>
