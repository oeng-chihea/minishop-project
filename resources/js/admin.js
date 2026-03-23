require('./bootstrap');

import { createApp } from 'vue';
import AdminDashboard from './components/admin/AdminDashboard.vue';

createApp(AdminDashboard).mount('#admin-dashboard');
