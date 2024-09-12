<template>
  
    <div class="dashboard">
      <div class="container">
      <h1>Dashboard</h1>
      <p>Randevu yönetim sistemine hoş geldiniz.</p>
    </div>
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
align-items: center;
text-align: center;
  box-sizing: border-box;
  width: 100%;
  position: absolute;
  right: 0;
}
.container{
  margin-top: 200px;
  padding-left: 120px;
}
.dashboard h2 {
  font-size: 28px;
  margin-bottom: 20px;
  margin-top: 100px;
}


/* Mobil uyum için düzen */
@media (max-width: 767px) {
  .dashboard {
    padding: 15px;
  width: 90%;
  position: absolute;
  right: 0;
}

  .dashboard h2 {
    font-size: 24px;
  }

}
</style>