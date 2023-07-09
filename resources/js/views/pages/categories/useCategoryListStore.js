import axios from '@axios'
import { defineStore } from 'pinia'

export const useCategoryListStore = defineStore('CategoryListStore', {
  actions: {
    // 👉 Fetch categories data
    fetchCategories(params) { return axios.get('/categories', { params }) },

    // 👉 Add Category
    addCategory(categoryData) {
      return new Promise((resolve, reject) => {
        axios.post('/categories', categoryData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single category
    fetchCategory(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/categories/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update category
    updateCategory(id, categoryData) {
      return new Promise((resolve, reject) => {
        axios.put(`/categories/${id}`, categoryData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete Category
    deleteCategory(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/categories/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
