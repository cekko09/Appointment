import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '../components/Dashboard.vue';
import AppointmentList from '../components/AppointmentList.vue';
import AppointmentForm from '../components/AppointmentForm.vue';
import AppointmentEdit from '../components/AppointmentEdit.vue';

import Login from '@/components/Login.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Employee from '@/components/Employee.vue';
import AddEmployee from '@/components/AddEmployee.vue';

const routes = [
  {
    path: '/',
    component: AuthLayout, // Giriş yapılmadığında kullanılacak layout
    children: [
      {
        path: '',
        name: 'Login',
        component: Login,
      },
    ],
  },
  {
    path: '/dashboard',
    component: MainLayout, // Giriş yapıldıktan sonra kullanılacak layout
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }, // Auth gerektiren rota
      },
      { path: '/appointments', component: AppointmentList , meta: { requiresAuth: true, roles: ['employee', 'admin'] },},
      { path: '/appointments/new', component: AppointmentForm , meta: { requiresAuth: true, roles: ['employee', 'admin'] },},
      { path: '/appointments/edit/:id', component: AppointmentEdit,meta: { requiresAuth: true, roles: ['employee', 'admin'] }, },
      {
        path: '/add-employee',
        name: 'AddEmployee',
        component: AddEmployee,
        meta: { requiresAuth: true, roles: ['admin'] },
      },
      {
        path: '/employees',
        name: 'Employee',
        component: Employee,
        meta: { requiresAuth: true, roles: ['admin'] },
      },
      {
        path: '/AppointmentEdit/:id',
        name: 'AppointmentEdit',
        component: AppointmentEdit,
        meta: { requiresAuth: true, roles: ['employee', 'admin'] }, 
      }
    ],
  },
 
 
 
];

const router = createRouter({
  history: createWebHistory(),
  routes
});
router.beforeEach((to, from, next) => {
  const isLoggedIn = !!localStorage.getItem('token');
  const userRole = localStorage.getItem('userRole'); // Kullanıcı rolünü al

  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isLoggedIn) {
      next({ name: 'Login' });
    } else if (to.meta.roles && !to.meta.roles.includes(userRole)) {
      next({ name: 'Appointments' }); // Erişim yetkisi yoksa yönlendirme
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
