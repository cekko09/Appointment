<template>
  <aside :class="['sidebar', { open: isSidebarOpen }]">
    <div class="logo">
      <h1>Iceberg Estates</h1>
    </div>
    <div class="user_info">
      <h2 v-if="userName">{{ userName + ' ,' + userRole }}</h2> <!-- Kullanıcının adını göster -->
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
        <li v-show="userRole == 'admin'"><router-link to="/add-employee">Çalışan Ekle</router-link></li>
        <li v-show="userRole == 'admin'"><router-link to="/employees">Çalışanlar</router-link></li>
        <li>
          <button @click="logout">Logout</button>
        </li>
      </ul>
    </nav>
    <!-- Aç/Kapa Butonu -->
    <button class="toggle-button" @click="toggleSidebar">
      <span v-if="!isSidebarOpen">&#9776; </span>
      <span v-else class="close-button">&times;</span> <!-- Sağ üste yerleşen kapatma ikonu -->
    </button>
  </aside>
</template>
<script>
import { useUserStore } from '@/stores/user';
import axios from 'axios';

export default {
  name: 'Sidebar',
  data() {
    return {
      userStore: useUserStore(),
      isSidebarOpen: false, // Mobilde menü başlangıçta kapalı
    };
  },
  computed: {
    userName() {
      return this.userStore.userName;
    },
    userRole( ) {
      return this.userStore.userRole;
    }
  },
  created() {
    this.userStore.fetchUser();
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen; // Menü aç/kapa işlevi
    },
    async logout() {
      try {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        localStorage.removeItem('userRole');
        localStorage.removeItem('token');
        window.dispatchEvent(new Event('storage'));

        this.$swal.fire({
          title: 'Başarılı!',
          text: 'Başarıyla çıkış yaptınız.',
          icon: 'success',
          confirmButtonText: 'Tamam'
        }).then(() => {
          this.$router.push('/');
        });
      } catch (error) {
        console.error('Logout failed', error);
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
  position: fixed; /* Menü ekranın üstünde kalır */
  top: 0;
  left: 0;
  z-index: 1000; /* Diğer componentlerin üstünde */
}

.sidebar.open {
  z-index: 1000; /* Açık olduğunda üstte kalmasını sağlar */
}

.logo{
  margin-top: 40px;
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

.toggle-button {
  display: none;
  position: fixed; /* Menü butonunun sabit olmasını sağlar */
  top: 10px;
  left: 10px;
  background-color: transparent;
  color: #ecf0f1;
  border: none;
  font-size: 24px;
  cursor: pointer;
  z-index: 1001; /* Diğer içeriklerin üstünde */
}

.close-button {
  font-size: 30px;
}

/* Mobil cihazlar için yan menüyü açma/kapatma */
@media (max-width: 768px) {
  .sidebar {
    width: 60px;
    padding: 10px;
    overflow: hidden;
    transition: width 0.3s ease;
  }

  .sidebar.open {
    width: 250px;
    z-index: 1000; /* Açık olduğunda üstte kalmasını sağlar */
  }

  .logo h1, .user_info, nav ul {
    display: none;
  }

  .sidebar.open .logo h1, .sidebar.open .user_info, .sidebar.open nav ul {
    display: block;
  }

  .toggle-button {
    display: block;
  }
}
</style>

