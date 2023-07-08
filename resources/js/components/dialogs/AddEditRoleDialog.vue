<script setup>
import { useSiteStore } from '@/views/useSiteStore'
import axios from '@axios'
import { VForm } from 'vuetify/components/VForm'

const props = defineProps({
  rolePermissions: {
    type: Object,
    required: false,
    default: () => ({
      id: null,
      name: '',
      permissions: [],
    }),
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'update:rolePermissions',
  'refetchRoles',
])

const { rolePermissions } = toRefs(props)

// 👉 Permission List
const permissions = ref([
  /**
   * Here you should hardcode your categorized roles.
   */
  {
    name: 'Roles',
    'Create role': false,
    'Read role': false,
    'Update role': false,
    'Delete role': false,
  },
  {
    name: 'Users',
    'Create user': false,
    'Read user': false,
    'Update user': false,
    'Delete user': false,
  },
])

const isSelectAll = ref(false)
const role = ref('')
const refPermissionForm = ref()

const errors = ref({
  name: undefined,
})

const checkedCount = computed(() => {
  let counter = 0
  permissions.value.forEach(permission => {
    Object.entries(permission).forEach(([key, value]) => {
      if (key !== 'name' && value)
        counter++
    })
  })

  return counter
})

const isIndeterminate = computed(() => checkedCount.value > 0 && checkedCount.value < permissions.value.length * 4)

// select all
watch(isSelectAll, val => {
  permissions.value = permissions.value.map(permission => {
    Object.keys(permission).forEach(function(key, index) {
      if (key !== 'name') {
        permission[key] = val
      }
    })

    return {
      ...permission,
    }
  })
})

// if Indeterminate is false, then set isSelectAll to false
// watch(isIndeterminate, () => {
//   if (!isIndeterminate.value)
//     isSelectAll.value = false
// })

// if all permissions are checked, then set isSelectAll to true
watch(permissions, () => {
  if (checkedCount.value === permissions.value.length * 4)
    isSelectAll.value = true

  if (checkedCount.value === 0)
    isSelectAll.value = false
}, { deep: true })

// if rolePermissions is not empty, then set permissions
watch(rolePermissions, val => {
  if (val && val.name) {
    role.value = val.name
    permissions.value = permissions.value.map(permission => {
      const rolePermission = val?.permissions.find(item => item.name === permission.name)
      if (rolePermission) {
        return {
          ...permission,
          ...rolePermission,
        }
      }

      return permission
    })
  }
})

const siteStore = useSiteStore()
const submitting = ref(false)

const onSubmit = async () => {
  submitting.value = true;

  const rolePermissions = {
    name: role.value,
    permissions: permissions.value,
  }

  try {
    // Update or Create on backend
    const response = props.rolePermissions.id ? await axios.put(`roles/${props.rolePermissions.id}`, rolePermissions) : await axios.post('roles', rolePermissions);

    const { message } = response.data;

    emit('update:rolePermissions', rolePermissions);
    emit('update:isDialogVisible', false);
    emit('refetchRoles');
    isSelectAll.value = false;
    refPermissionForm.value?.reset();

    siteStore.alert(message);
  } catch (error) {
    const response = error?.response;
    if (response?.status === 422) {
      errors.value = response?.data?.errors;
    }
  } finally {
    submitting.value = false;
  }
}

const onReset = () => {
  emit('update:isDialogVisible', false)
  isSelectAll.value = false
  refPermissionForm.value?.reset()
};
</script>

<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 900"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <!-- 👉 Dialog close btn -->
    <DialogCloseBtn @click="onReset" />

    <VCard class="pa-sm-8 pa-5">
      <!-- 👉 Title -->
      <VCardItem class="text-center">
        <VCardTitle class="text-h3 mb-3">
          {{ $t((props.rolePermissions.name ? 'Edit' : 'Add New') + ' Role') }}
        </VCardTitle>
        <p class="text-base mb-0">
          {{ $t('Set Role Permissions') }}
        </p>
      </VCardItem>

      <VCardText class="mt-6">
        <!-- 👉 Form -->
        <VForm ref="refPermissionForm">
          <!-- 👉 Role name -->
          <AppTextField
            v-model="role"
            :label="$t('Role Name')"
            :placeholder="$t('Enter Role Name')"
            :error-messages="errors?.name"
          />

          <h6 class="text-h4 mt-8 mb-3">
            {{ $t('Role Permissions') }}
          </h6>

          <!-- 👉 Role Permissions -->

          <VTable class="permission-table text-no-wrap">
            <!-- 👉 Admin  -->
            <tr>
              <td>
                {{ $t('Administrator Access') }}
              </td>
              <td colspan="4">
                <div class="d-flex justify-end">
                  <VCheckbox
                    v-model="isSelectAll"
                    v-model:indeterminate="isIndeterminate"
                    :label="$t('Select All')"
                  />
                </div>
              </td>
            </tr>

            <!-- 👉 Other permission loop -->
            <template
              v-for="permission in permissions"
              :key="permission.name"
            >
              <tr>
                <td>{{ $t(permission.name) }}</td>
                <template
                  v-for="key in Object.keys(permission)"
                  :key="key"
                >
                  <td v-if="key != 'name'">
                    <div class="d-flex justify-start">
                      <VCheckbox
                        v-model="permission[key]"
                        :label="$t(key)"
                      />
                    </div>
                  </td>
                </template>
              </tr>
            </template>
          </VTable>

          <!-- 👉 Actions button -->
          <div class="d-flex align-center justify-center gap-3 mt-6">
            <VBtn
              :disabled="submitting"
              @click.prevent="onSubmit"
            >
              {{ $t('Submit') }}
            </VBtn>

            <VBtn
              color="secondary"
              variant="tonal"
              @click="onReset"
            >
              {{ $t('Cancel') }}
            </VBtn>
          </div>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.permission-table {
  td {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    padding-block: 0.5rem;

    .v-checkbox {
      min-inline-size: 4.75rem;
    }

    &:not(:first-child) {
      padding-inline: 0.5rem;
    }

    .v-label {
      white-space: nowrap;
    }
  }
}
</style>
