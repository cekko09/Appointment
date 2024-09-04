<template>
    <aside class="sidebar">
      <div class="logo">
        <h1>Iceberg Estates</h1>
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
  import axios from 'axios';
  import { useRouter } from 'vue-router';
  export default {
    name: 'Sidebar',
    setup() {
    const router = useRouter();

   
    const logout = async () => {
      try {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        });
        localStorage.removeItem('token'); // Token'ı temizle
        // Giriş durumunu manuel olarak güncelle
        window.dispatchEvent(new Event('storage')); // Yeni bir event tetikleyin
        router.push('/');
      } catch (error) {
        console.error('Logout failed', error);
      }
    };

    return { logout };
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
  button{
    text-decoration: none;
    font-size: 18px;
    padding: 10px;
    border-radius: 4px;
    transition: background-color 0.3s;
    background-color: #2c3e50;
    color: #ecf0f1;
  }
  button:hover{
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
  