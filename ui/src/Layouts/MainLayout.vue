<!-- src/layouts/MainLayout.vue -->
<template>
    <div class="main-container ">
      <!-- Giriş yapıldıysa Sidebar gösterilecek -->
      <div>
        <Sidebar />
      </div>
     <div>
      <router-view></router-view>
     </div>
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
  widows: 100%;
}
</style>