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
  text-align: center;
    margin-left: 250px;
    position: relative;
    top: 50% !important;
    height: 100vh;
    transform: translateY(40%);
}
.container {
  padding: 0;
  margin: 0;
}
@media screen and (max-width: 576px) {
.dashboard {
  margin-left: 100px;
}
 }
</style>