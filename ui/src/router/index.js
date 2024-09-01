import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import AppointmentList from '../components/AppointmentList.vue';
import AppointmentForm from '../components/AppointmentForm.vue';
import AppointmentEdit from '../components/AppointmentEdit.vue';

const routes = [
  { path: '/', component: Dashboard },
  { path: '/appointments', component: AppointmentList },
  { path: '/appointments/new', component: AppointmentForm },
  { path: '/appointments/edit/:id', component: AppointmentEdit }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
