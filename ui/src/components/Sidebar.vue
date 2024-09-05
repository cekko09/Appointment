<!-- Sidebar.vue -->
<template>
  <aside class="sidebar">
    <div class="logo">
      <h1>Iceberg Estates</h1>
    </div>
    <div class="user_info">
      <h2 v-if="userName">{{ userName }}</h2> <!-- Kullanıcının adını göster -->
      <h6 v-else>Kullanıcı Bilgileri Yükleniyor...</h6>
    </div>
    <nav>
      <ul>
        <li>
          <router-link to="/dashboard">Dashboard</router-link>
        </li>
        <li>
          <router-link to="/appointments">Randevular</router-link>
        </li>
        <li>
          <router-link to="/appointments/new">Randevu Oluştur</router-link>
        </li>
        <li><router-link to="/add-employee">Çalışan Ekle</router-link></li>
        <li><router-link to="/employees">Çalışanlar</router-link></li>
        <li>
          <button @click="logout">Logout</button>
        </li>
      </ul>
    </nav>
  </aside>
</template>

<script>
import { useUserStore } from '@/stores/user';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
  name: 'Sidebar',
  data() {
    return {
      userStore: useUserStore(), // Kullanıcı store'u
    };
  },
  computed: {
    userName() {
      return this.userStore.userName; // Kullanıcının adı
    },
  },
  created() {
    this.userStore.fetchUser(); // Bileşen yüklendiğinde kullanıcıyı yükle
  },
  methods: {
    async logout() {
      try {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        localStorage.removeItem('userRole'); // Kullanıcı rolünü temizle
        localStorage.removeItem('token'); // Token'ı temizle
        window.dispatchEvent(new Event('storage')); // Yeni bir event tetikleyin

        // SweetAlert2 ile logout başarısı mesajı
        this.$swal.fire({
          title: 'Başarılı!',
          text: 'Başarıyla çıkış yaptınız.',
          icon: 'success',
          confirmButtonText: 'Tamam'
        }).then(() => {
          this.$router.push('/'); // Kullanıcı onay verdikten sonra yönlendirin
        });

      } catch (error) {
        console.error('Logout failed', error);
        
        // SweetAlert2 ile hata mesajı
        this.$swal.fire({
          title: 'Hata!',
          text: 'Çıkış yapılamadı. Lütfen tekrar deneyin.',
          icon: 'error',
          confirmButtonText: 'Tamam'
        });
      }
    },
  },
};
</script>

<style scoped>

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  min-height: 100vh;
  padding: 20px;
  color: #ecf0f1;
  transition: width 0.3s ease;
}

.logo h1 {
  font-size: 24px;
  margin-bottom: 30px;
  color: #ecf0f1;
}

nav ul {
  list-style-type: none;
  padding: 0;
}

nav ul li {
  margin: 20px 0;
}

nav ul li a {
  text-decoration: none;
  color: #ecf0f1;
  font-size: 18px;
  display: block;
  padding: 10px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

nav ul li a:hover {
  background-color: #34495e;
}

button {
  text-decoration: none;
  font-size: 18px;
  padding: 10px;
  border-radius: 4px;
  transition: background-color 0.3s;
  background-color: #2c3e50;
  color: #ecf0f1;
}

button:hover {
  background-color: #34495e;
}

/* Mobil cihazlar için yan menüyü gizleme ve açma */
@media (max-width: 768px) {
  .sidebar {
    width: 60px;
    padding: 10px;
  }

  .logo h1 {
    display: none;
  }

  nav ul li a {
    font-size: 16px;
    padding: 8px;
  }
}
</style>
