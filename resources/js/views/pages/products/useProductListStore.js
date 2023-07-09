import axios from '@axios'
import { defineStore } from 'pinia'

export const useProductListStore = defineStore('ProductListStore', {
  actions: {
    // 👉 Fetch products data
    fetchProducts(params) { return axios.get('/products', { params }) },

    // 👉 Add Product
    addProduct(productData) {
      return new Promise((resolve, reject) => {
        axios.post('/products', productData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single product
    fetchProduct(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/products/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update product
    updateProduct(id, productData) {
      return new Promise((resolve, reject) => {
        axios.post(`/products/${id}`, productData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete Product
    deleteProduct(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/products/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Export products data
    exportProducts(params) { return axios.get('/products/export', { params }) },
  },
})
