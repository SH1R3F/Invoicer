<script setup>
import RoleCards from '@/views/pages/roles/RoleCards.vue'
import RequestLoader from '@/layouts/components/RequestLoader.vue'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <h4 class="text-h4 mb-6">
        {{ $t('Roles List') }}
      </h4>
      <p class="mb-0">
        {{ $t('A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what he need') }}
      </p>
    </VCol>

    <!-- 👉 Roles Cards -->
    <VCol cols="12">
      <Suspense>
        <RoleCards />
        <template #fallback>
          <RequestLoader />
        </template>
      </Suspense>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
meta:
  action: Read
  subject: role
  title: Roles
</route>
