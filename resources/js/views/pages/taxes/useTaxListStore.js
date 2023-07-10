import axios from '@axios'
import { defineStore } from 'pinia'

export const useTaxListStore = defineStore('TaxListStore', {
  actions: {
    // 👉 Fetch taxes data
    fetchTaxes(params) { return axios.get('/taxes', { params }) },

    // 👉 Add Tax
    addTax(taxData) {
      return new Promise((resolve, reject) => {
        axios.post('/taxes', taxData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single tax
    fetchTax(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/taxes/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update tax
    updateTax(id, taxData) {
      return new Promise((resolve, reject) => {
        axios.put(`/taxes/${id}`, taxData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete Tax
    deleteTax(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/taxes/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
