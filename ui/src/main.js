import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { createPinia } from 'pinia'; 

axios.defaults.baseURL = 'http://localhost:8000/api';
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
const app = createApp(App);
const pinia = createPinia(); 

app.use(pinia); 
app.use(router);

app.mount('#app');