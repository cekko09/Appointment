<template>
    <aside class="sidebar">
      <div class="logo">
        <h1>Iceberg Estates</h1>
      </div>
      <nav>
        <ul>
          <li>
            <router-link to="/">Dashboard</router-link>
          </li>
          <li>
            <router-link to="/appointments">Randevular</router-link>
          </li>
          <li>
            <router-link to="/appointments/new">Randevu Oluştur</router-link>
          </li>
        </ul>
      </nav>
    </aside>
  </template>
  
  <script>
  export default {
    name: 'Sidebar'
  };
  </script>
  <style scoped>
  .sidebar {
    width: 250px;
    background-color: #2c3e50;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
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
  