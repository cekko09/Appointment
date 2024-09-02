<!-- src/layouts/MainLayout.vue -->
<template>
    <div class="main-container">
      <!-- Giriş yapıldıysa Sidebar gösterilecek -->
      <Sidebar />
      <router-view></router-view>
    </div>
  </template>
  
  <script>
  import Sidebar from '../components/Sidebar.vue';
  
  export default {
    name: 'MainLayout',
    components: {
      Sidebar,
    },
  };
  </script>
  <style scoped>
.main-container {
  display: flex;
}
</style>