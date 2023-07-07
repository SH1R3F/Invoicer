<script setup>
import { useSiteStore } from '@/views/useSiteStore';
import { storeToRefs } from 'pinia';
import { onMounted, onUnmounted } from 'vue';

const { loading } = storeToRefs(useSiteStore())

onMounted(() => {
  loading.value = true;
})

onUnmounted(() => {
  loading.value = false;
});
</script>

<template>
  <div class="text-center my-5">
    <VProgressCircular
      :size="50"
      color="primary"
      indeterminate
    />
  </div>
</template>
