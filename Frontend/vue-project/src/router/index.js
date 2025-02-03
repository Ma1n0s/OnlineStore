import { createRouter, createWebHistory } from 'vue-router';
import Main from '../views/Main.vue';
import About from '../views/About.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Main',
      component: Main,
    },
    {
      path: '/about',
      name: 'About',
      component: About,
    },

  ]
})

export default router;
