// stores/userStore.js
import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('userStore', {
  state: () => ({
    user: null, // Kullanıcı bilgilerini saklayacağımız state
  }),
  actions: {
    async fetchUser() {
      try {
        const response = await axios.get('http://localhost:8000/api/user', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        this.user = response.data; // Kullanıcı bilgilerini state'e kaydet
      } catch (error) {
        console.error('Kullanıcı bilgileri çekilemedi:', error);
        this.user = null; // Hata durumunda kullanıcı bilgisini sıfırla
      }
    },
  },
  getters: {
    userName(state) {
      return state.user ? state.user.name : ''; // Kullanıcının adını döndüren getter
    },
    userRole(state) {
      return state.user ? state.user.role : ''; // Kullanıcının rolünü döndüren getter
    },
  },
});
