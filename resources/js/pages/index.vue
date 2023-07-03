<script setup>
import ability from '@/plugins/casl/ability'
import axios from '@axios'

// Send a request to validate user
axios.get('/user').then(response => {
  const { userData, userAbilities } = response.data

  localStorage.setItem('userAbilities', JSON.stringify(userAbilities))
  ability.update(userAbilities)
  localStorage.setItem('userData', JSON.stringify(userData))
}).catch(e => {
  console.error(e)
})
</script>

<template>
  <div>
    <VCard
      class="mb-6"
      title="Kick start your project 🚀"
    >
      <VCardText>All the best for your new project.</VCardText>
      <VCardText>
        Please make sure to read our <a
          href="https://demos.pixinvent.com/vuexy-vuejs-admin-template/documentation/"
          target="_blank"
          rel="noopener noreferrer"
          class="text-decoration-none"
        >
          Template Documentation
        </a> to understand where to go from here and how to use our template.
      </VCardText>
    </VCard>

    <VCard title="Want to integrate JWT? 🔒">
      <VCardText>We carefully crafted JWT flow so you can implement JWT with ease and with minimum efforts.</VCardText>
      <VCardText>Please read our  JWT Documentation to get more out of JWT authentication.</VCardText>
    </VCard>
  </div>
</template>

<route lang="yaml">
meta:
  title: Dashboard
  authenticated: true
</route>
