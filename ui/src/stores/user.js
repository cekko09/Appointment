import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
  }),
  actions: {
    async fetchUser() {
      try {
        const response = await axios.get('http://localhost:8000/api/user'); // Kullanıcı bilgilerini backend'den çek
        this.user = response.data; // Kullanıcı bilgilerini store'a kaydet
      } catch (error) {
        console.error('Kullanıcı bilgileri alınamadı:', error);
      }
    },
  },
  getters: {
    userRole: (state) => state?.user?.role ,
  },
});
