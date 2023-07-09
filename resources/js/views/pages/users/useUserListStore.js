import axios from '@axios'
import { defineStore } from 'pinia'

export const useUserListStore = defineStore('UserListStore', {
  state: () => {
    return {
      userData: JSON.parse(localStorage.getItem('userData') || 'null'),
    }
  },

  actions: {
    // 👉 Fetch users data
    fetchUsers(params) { return axios.get('/users', { params }) },

    // 👉 Add User
    addUser(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/users', userData).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 fetch single user
    fetchUser(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 fetch current user
    fetchMe() {
      return new Promise((resolve, reject) => {
        axios.get('/user').then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update user
    updateUser(id, userData) {
      return new Promise((resolve, reject) => {
        axios.post(`/users/${id}`, userData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update current user
    updateMe(userData) {
      return new Promise((resolve, reject) => {
        axios.post('/account-settings/account', userData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 update current user's password
    updatePassword(passwordData) {
      return new Promise((resolve, reject) => {
        axios.put('/account-settings/password', passwordData).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete User
    deleteUser(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Fetch roles
    fetchRoles() { return axios.get('/roles') },

    // 👉 Export users data
    exportUsers(params) { return axios.get('/users/export', { params }) },

  },
})
