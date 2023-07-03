import { defineStore } from 'pinia'

export const useSiteStore = defineStore('SiteStore', {
  state: () => {
    return {
      alertStatus: false,
      alertColor: 'success',
      alertMessage: '',
    }
  },

  actions: {
    alert(message, color = 'success') {
      this.alertMessage = message
      this.alertColor = color
      this.alertStatus = true
    },
  },

})
