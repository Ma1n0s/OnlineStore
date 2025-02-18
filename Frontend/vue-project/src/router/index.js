import { createRouter, createWebHistory } from 'vue-router';
import Main from '../views/Main.vue';
import About from '../views/About.vue';
import StroitelnyjInstrument from '@/views/category/stroitelnyj-instrument.vue';
import Shurupoverty from '@/views/category/shurupoverty.vue';
import Publication from '@/views/publication.vue';
import CourierDelivery from '@/views/Courier-delivery.vue';

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
    {
      path: '/category/troitelnyj-instrument',
      name: 'Stroitelnyj-instrument',
      component: StroitelnyjInstrument,
    },
    {
      path: '/category/shurupoverty',
      name: 'Shurupoverty',
      component: Shurupoverty,
    },
    {
      path: '/publication',
      name: 'Publication',
      component: Publication,
    },
    {
      path: '/courier-delivery',
      name: 'Courier-delivery',
      component: CourierDelivery,
    },

  ]
})

export default router;
