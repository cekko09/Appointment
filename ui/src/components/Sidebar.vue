<template>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark menu_container">
        <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white min-vh-100">
          <router-link to="/dashboard"
            class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <span class=" d-none d-sm-inline brand_name">Randevu Yönetim</span>
          </router-link>



          <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
            <li class="nav-item">
              <router-link to="/dashboard" class="nav-link align-middle px-0">
                <i class="fa-solid fa-house"></i> <span class="ms-1 fs-5 d-none d-sm-inline">Dashboard</span>
              </router-link>
            </li>

            <li class="nav-item">
              <router-link to="/appointments" class="nav-link px-0">
                <i class="fa-solid fa-calendar-check"></i> <span class="d-none ms-1 fs-5 d-sm-inline">Tüm
                  Randevular</span>
              </router-link>
            </li>
            <li class="nav-item">
              <router-link to="/appointments/new" class="nav-link px-0">
                <i class="fa-regular fa-calendar-plus"></i> <span class="d-none ms-1 fs-5 d-sm-inline">Yeni
                  Randevu</span>
              </router-link>
            </li>
            <li class="nav-item" v-show="userRole === 'admin'">
              <router-link to="/employees" class="nav-link px-0 align-middle">
                <i class="fa-solid fa-user"></i> <span class="ms-1 d-none fs-5 d-sm-inline">Çalışanlar</span>
              </router-link>
            </li>
            <li class="nav-item" v-show="userRole === 'admin'">
              <router-link to="/add-employee" class="nav-link px-0 align-middle">
                <i class="fa-solid fa-user-plus"></i> <span class="ms-1 d-none fs-5 d-sm-inline">Çalışan Ekle</span>
              </router-link>
            </li>

            <li>
              <button @click="logout" class="btn btn-danger w-100 mt-3"><i
                  class="fa-solid fa-right-from-bracket"></i></button>
            </li>
          </ul>

          <hr>
          <div class="dropdown pb-4">
            <i class="fa-solid fa-user-tie me-1"></i>
            <span class="d-none d-sm-inline mx-1 ">{{ userName }}</span>
            <span class="user_role">{{ userRole }}</span>

          </div>
        </div>
      </div>


    </div>
  </div>
</template>

<script>
import { useUserStore } from '@/stores/user';
import axios from 'axios';

export default {
  name: 'Sidebar',
  data() {
    return {
      userStore: useUserStore(),
    };
  },
  computed: {
    userName() {
      return this.userStore.userName;
    },
    userRole() {
      return this.userStore.userRole;
    },
  },
  created() {
    this.userStore.fetchUser();
  },
  methods: {
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
          confirmButtonText: 'Tamam',
        }).then(() => {
          this.$router.push('/');
        });
      } catch (error) {
        console.error('Logout failed', error);
        this.$swal.fire({
          title: 'Hata!',
          text: 'Çıkış yapılamadı. Lütfen tekrar deneyin.',
          icon: 'error',
          confirmButtonText: 'Tamam',
        });
      }
    },
  },
};
</script>

<style scoped>
.menu_container {
  position: fixed;
  min-height: 100vh;
  width: 250px;
}

.content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

.dropdown {
  margin-top: auto;
}

.user_role {
  font-size: 12px;
  font-weight: bold;
  color: #999;
}

i {
  font-size: 2rem;
}

.nav-item span {
  color: white;
}

.nav-item {
  padding: 10px 0 10px 0;
}

.brand_name {
  font-size: 1.6rem;
  font-weight: bold;
}

@media screen and (max-width: 576px) {
  .menu_container {
    width: 100px;
  }
}
</style>
