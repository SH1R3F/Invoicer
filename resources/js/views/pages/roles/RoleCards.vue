<script setup>
import { useSiteStore } from '@/views/useSiteStore'
import axios from '@axios'
import manUsingTab from '@images/pages/create-deal-review-complete.png'

let roles = ref([])

const siteStore = useSiteStore()

const fetchRoles = () => {
  axios.get('roles').then(response => {
    roles.value = response.data
  })
}

fetchRoles()

const isRoleDialogVisible = ref(false)
const roleDetail = ref()
const isAddRoleDialogVisible = ref(false)

const editPermission = value => {
  isRoleDialogVisible.value = true
  roleDetail.value = value
}

const deleteRole = id => {
  axios.delete(`roles/${id}`).then(response => {
    const { status, message } = response.data
    if (status == 'success') {
      roles.value = roles.value.filter(role => role.id != id)
      siteStore.alert(message)
    }
  })
}
</script>

<template>
  <VRow>
    <!-- 👉 Roles -->
    <VCol
      v-for="item in roles"
      :key="item.role"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard class="h-100">
        <VCardText class="d-flex align-center pb-1">
          <span>{{ $t('Total {number} users', {number: item.users.length}) }}</span>

          <VSpacer />

          <div class="v-avatar-group">
            <template
              v-for="(user, index) in item.users"
              :key="user"
            >
              <VAvatar
                v-if="item.users.length > 4 && item.users.length !== 4 && index < 3"
                size="36"
                :image="user"
              />

              <VAvatar
                v-if="item.users.length === 4"
                size="36"
                :image="user"
              />
              
              <VAvatar
                v-if="item.users.length <= 4"
                size="36"
                :image="user"
              />
            </template>
            <VAvatar
              v-if="item.users.length > 4"
              :color="$vuetify.theme.current.dark ? '#4A5072' : '#f6f6f7'"
            >
              <span>
                +{{ item.users.length - 3 }}
              </span>
            </VAvatar>
          </div>
        </VCardText>

        <VCardText class="pb-5">
          <h4 class="text-h4">
            {{ item.role }}
          </h4>
          <div class="d-flex align-center">
            <a
              v-if="item.editable"
              href="javascript:void(0)"
              @click="editPermission(item.details)"
            >
              {{ $t('Edit Role') }}
            </a>

            <VSpacer />
            <VBtn
              v-if="item.deletable"
              icon
              color="error"
              variant="text"
              size="x-small"
              @click="deleteRole(item.id)"
            >
              <VIcon
                size="24"
                icon="tabler-trash"
              />
            </VBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Add New Role -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard
        class="h-100"
        :ripple="false"
        @click="isAddRoleDialogVisible = true"
      >
        <VRow
          no-gutters
          class="h-100"
        >
          <VCol
            cols="5"
            class="d-flex flex-column justify-end align-center mt-5"
          >
            <img
              width="85"
              :src="manUsingTab"
            >
          </VCol>

          <VCol cols="7">
            <VCardText
              class="d-flex flex-column align-end justify-end gap-2 h-100"
              style="text-align: end;"
            >
              <VBtn>{{ $t('Add New Role') }}</VBtn>
              <span>{{ $t("Add role, if it doesn't exist.") }}</span>
            </VCardText>
          </VCol>
        </VRow>
      </VCard>
      <AddEditRoleDialog
        v-model:is-dialog-visible="isAddRoleDialogVisible"
        @refetch-roles="fetchRoles"
      />
    </VCol>
  </VRow>

  <AddEditRoleDialog
    v-model:is-dialog-visible="isRoleDialogVisible"
    v-model:role-permissions="roleDetail"
    @refetch-roles="fetchRoles"
  />
</template>
