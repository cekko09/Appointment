<template>
  
    <div class="dashboard">
      <h1>Dashboard</h1>
      <p>Randevu yönetim sistemine hoş geldiniz.</p>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Dashboard'
  };
  </script>
 
<style scoped>
.dashboard {
  padding: 20px;
  background-color: #ecf0f1;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  width: 100%;
}

.dashboard h2 {
  font-size: 28px;
  margin-bottom: 20px;
}

.content {
  width: 100%;
  max-width: 1200px;
  background: #ffffff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Mobil uyum için düzen */
@media (max-width: 768px) {
  .dashboard {
    margin-left: 0;
    padding: 15px;
  }

  .dashboard h2 {
    font-size: 24px;
  }

  .content {
    padding: 15px;
  }
}
</style>