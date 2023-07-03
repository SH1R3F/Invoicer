<script setup>
import AddNewUserDrawer from '@/views/pages/users/AddNewUserDrawer.vue'
import { useUserListStore } from '@/views/pages/users/useUserListStore'
import { useSiteStore } from '@/views/useSiteStore'
import { avatarText } from '@core/utils/formatters'
import { paginationMeta } from '@layouts/utils'
import { useI18n } from 'vue-i18n'
import { VDataTableServer } from 'vuetify/labs/VDataTable'

const userListStore = useUserListStore()
const searchQuery = ref('')
const selectedRole = ref()
const selectedStatus = ref()
const totalPage = ref(1)
const totalUsers = ref(0)
const users = ref([])

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

const { t } = useI18n()

// Headers
const headers = [
  {
    title: t('User'),
    key: 'user',
  },
  {
    title: t('Role'),
    key: 'role',
  },
  {
    title: t('Email'),
    key: 'email',
  },
  {
    title: t('Status'),
    key: 'status',
  },
  {
    title: t('Actions'),
    key: 'actions',
    sortable: false,
  },
]

// 👉 Stats
const userStats = ref([])

// 👉 Fetching users
const fetchUsers = () => {
  userListStore.fetchUsers({
    q: searchQuery.value,
    status: selectedStatus.value,
    role: selectedRole.value,
    options: options.value,
  }).then(response => {
    users.value = response.data.data
    totalPage.value = response.data.meta.last_page
    totalUsers.value = response.data.meta.total
    options.value.page = response.data.meta.current_page

    const { total, superadmins, active, inactive } = response.data.stats

    // Update stats
    userStats.value = [
      {
        icon: 'tabler-user',
        color: 'primary',
        title: 'Total',
        stats: total,
        subtitle: 'Total Users',
      },
      {
        icon: 'tabler-user-check',
        color: 'success',
        title: 'Superadmins',
        stats: superadmins,
        subtitle: 'Total superadmins',
      },
      {
        icon: 'tabler-user-exclamation',
        color: 'warning',
        title: 'Active Users',
        stats: active,
        subtitle: 'Total active users',
      },
      {
        icon: 'tabler-user-plus',
        color: 'error',
        title: 'Inactive Users',
        stats: inactive,
        subtitle: 'Total inactive users',
      },
    ]
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchUsers)

// 👉 search filters
const roles = [
  {
    title: 'مدير',
    value: 'superadmin',
  },
  {
    title: 'مستخدم',
    value: 'user',
  },
]

const status = [
  {
    title: 'فعال',
    value: 1,
  },
  {
    title: 'غير فعال',
    value: 0,
  },
]

const resolveUserRoleVariant = role => {
  const roleLowerCase = role?.toLowerCase()
  if (roleLowerCase === 'superadmin')
    return {
      color: 'success',
      icon: 'tabler-user',
    }
  if (roleLowerCase === 'user')
    return {
      color: 'secondary',
      icon: 'tabler-device-laptop',
    }
  
  return {
    color: 'primary',
    icon: 'tabler-user',
  }
}

const resolveUserStatusVariant = stat => {
  if (stat == 1)
    return 'success'
  if (stat == 0)
    return 'warning'
  
  return 'primary'
}

const isAddNewUserDrawerVisible = ref(false)

const deleteUser = id => {
  userListStore.deleteUser(id).then(response => {
    const { status, message } = response.data
    if (status == 'success') {
      useSiteStore().alert(message)

      // refetch Users
      fetchUsers()
    }
  })

}


const exportUsers = () => {
  userListStore.exportUsers({
    q: searchQuery.value,
    status: selectedStatus.value,
    role: selectedRole.value,
    options: options.value,
  }).then(response => {
    const { status, url } = response.data
    if (status == 'success') {
      window.location = url
    }
  })
}
</script>

<template>
  <section>
    <VRow>
      <VCol
        v-for="meta in userStats"
        :key="meta.title"
        cols="12"
        sm="6"
        lg="3"
      >
        <VCard>
          <VCardText class="d-flex justify-space-between">
            <div>
              <span>{{ $t(meta.title) }}</span>
              <div class="d-flex align-center gap-2 my-1">
                <h6 class="text-h4">
                  {{ meta.stats }}
                </h6>
              </div>
              <span>{{ $t(meta.subtitle) }}</span>
            </div>

            <VAvatar
              rounded
              variant="tonal"
              :color="meta.color"
              :icon="meta.icon"
            />
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12">
        <VCard :title="$t('Search Filter')">
          <!-- 👉 Filters -->
          <VCardText>
            <VRow>
              <!-- 👉 Select Role -->
              <VCol
                cols="12"
                sm="6"
              >
                <AppSelect
                  v-model="selectedRole"
                  :label="$t('Select Role')"
                  :items="roles"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
              <!-- 👉 Select Status -->
              <VCol
                cols="12"
                sm="6"
              >
                <AppSelect
                  v-model="selectedStatus"
                  :label="$t('Select Status')"
                  :items="status"
                  clearable
                  clear-icon="tabler-x"
                />
              </VCol>
            </VRow>
          </VCardText>

          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div class="me-3 d-flex gap-3">
              <AppSelect
                :model-value="options.itemsPerPage"
                :items="[
                  { value: 10, title: '10' },
                  { value: 25, title: '25' },
                  { value: 50, title: '50' },
                  { value: 100, title: '100' },
                  { value: -1, title: 'All' },
                ]"
                style="width: 6.25rem;"
                @update:model-value="options.itemsPerPage = parseInt($event, 10)"
              />
            </div>
            <VSpacer />

            <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="inline-size: 10rem;">
                <AppTextField
                  v-model="searchQuery"
                  :placeholder="$t('Search')"
                  density="compact"
                />
              </div>

              <!-- 👉 Export button -->
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
                @click="exportUsers"
              >
                {{ $t('Export') }}
              </VBtn>  

              <!-- 👉 Add user button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewUserDrawerVisible = true"
              >
                {{ $t('Add New User') }}
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <!-- SECTION datatable -->
          <VDataTableServer
            v-model:items-per-page="options.itemsPerPage"
            v-model:page="options.page"
            :items="users"
            :items-length="totalUsers"
            :headers="headers"
            class="text-no-wrap"
            @update:options="options = $event"
          >
            <!-- User -->
            <template #item.user="{ item }">
              <div class="d-flex align-center">
                <VAvatar
                  size="34"
                  :variant="!item.raw.avatar ? 'tonal' : undefined"
                  :color="!item.raw.avatar ? resolveUserRoleVariant(item.raw.role).color : undefined"
                  class="me-3"
                >
                  <VImg
                    v-if="item.raw.avatar"
                    :src="item.raw.avatar"
                  />
                  <span v-else>{{ avatarText(item.raw.fullName) }}</span>
                </VAvatar>

                <div class="d-flex flex-column">
                  <h6 class="text-base">
                    <RouterLink
                      :to="{ name: 'users-id', params: { id: item.raw.id } }"
                      class="font-weight-medium user-list-name"
                    >
                      {{ item.raw.fullName }}
                    </RouterLink>
                  </h6>

                  <span class="text-sm text-medium-emphasis">@{{ item.raw.email }}</span>
                </div>
              </div>
            </template>

            <!-- 👉 Role -->
            <template #item.role="{ item }">
              <div class="d-flex align-center gap-4">
                <VAvatar
                  :size="30"
                  :color="resolveUserRoleVariant(item.raw.role).color"
                  variant="tonal"
                >
                  <VIcon
                    :size="20"
                    :icon="resolveUserRoleVariant(item.raw.role).icon"
                  />
                </VAvatar>
                <span class="text-capitalize">{{ item.raw.role || $t('No role') }}</span>
              </div>
            </template>

            <!-- Plan -->
            <template #item.plan="{ item }">
              <span class="text-capitalize font-weight-medium">{{ item.raw.currentPlan }}</span>
            </template>

            <!-- Status -->
            <template #item.status="{ item }">
              <VChip
                :color="resolveUserStatusVariant(item.raw.status)"
                size="small"
                label
                class="text-capitalize"
              >
                {{ $t(item.raw.status ? 'Active' : 'Inactive') }}
              </VChip>
            </template>

            <!-- Actions -->
            <template #item.actions="{ item }">
              <IconBtn :to="{ name: 'users-id', params: { id: item.raw.id } }">
                <VIcon icon="tabler-edit" />
              </IconBtn>

              <IconBtn @click="deleteUser(item.raw.id)">
                <VIcon icon="tabler-trash" />
              </IconBtn>
            </template>

            <!-- pagination -->
            <template #bottom>
              <VDivider />
              <div class="d-flex align-center justify-sm-space-between justify-center flex-wrap gap-3 pa-5 pt-3">
                <p class="text-sm text-disabled mb-0">
                  {{ paginationMeta(options, totalUsers) }}
                </p>

                <VPagination
                  v-model="options.page"
                  :length="Math.ceil(totalUsers / options.itemsPerPage)"
                  :total-visible="$vuetify.display.xs ? 1 : Math.ceil(totalUsers / options.itemsPerPage)"
                >
                  <template #prev="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      {{ $t('Previous') }}
                    </VBtn>
                  </template>

                  <template #next="slotProps">
                    <VBtn
                      variant="tonal"
                      color="default"
                      v-bind="slotProps"
                      :icon="false"
                    >
                      {{ $t('Next') }}
                    </VBtn>
                  </template>
                </VPagination>
              </div>
            </template>
          </VDataTableServer>
          <!-- SECTION -->
        </VCard>

        <!-- 👉 Add New User -->
        <AddNewUserDrawer
          v-model:isDrawerOpen="isAddNewUserDrawerVisible"
          @user-added="fetchUsers"
        />
      </vcol>
    </vrow>
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-medium-emphasis-opacity));
}
</style>

<route lang="yaml">
meta:
  action: Read
  subject: user
  title: Users
</route>
