<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
import Sidebar from './components/Sidebar.vue';

export default {
  components: {
    Sidebar
  }
};
</script>


<style>
body {
  margin: 0;
  padding: 0;
  height: 100vh;
}
#app {
  height: 100%;
}
.main-container {
  height: 100%;
}
</style>
