<script setup>
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import UserBioPanel from '@/views/pages/users/view/UserBioPanel.vue'
import UserTabAccount from '@/views/pages/users/view/UserTabAccount.vue'

const userListStore = useUserListStore()
const route = useRoute()
const userData = ref()
const roles = ref([])


try {
  const response = await userListStore.fetchUser(Number(route.params.id));

  const { user, roles: roleNames } = response.data;

  userData.value = user
  roles.value = roleNames
} catch (error) {
  console.error(error);
}

const updateUser = user => {
  userData.value = user
};
</script>

<template>
  <VRow>
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
