import { createRouter, createWebHistory } from 'vue-router';
import Main from '../views/Main.vue';
import About from '../views/About.vue';
import StroitelnyjInstrument from '@/views/category/stroitelnyj-instrument.vue';
import Shurupoverty from '@/views/category/shurupoverty.vue';
import Publication from '@/views/publication.vue';
import CourierDelivery from '@/views/Courier-delivery.vue';
import RegionDelivery from '@/views/region-delivery.vue';
import RussianPost from '@/views/delivery/russian-post.vue';
import Favorites from '@/views/profile/favorites.vue';
import Compare from '@/views/profile/compare.vue';
import Orders from '@/views/profile/orders.vue';
import CartCheckout from '@/views/profile/cart-checkout.vue';
import ProductCard from '@/views/product/productCard.vue';

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
    {
      path: '/region-delivery',
      name: 'Region-delivery',
      component: RegionDelivery,
    },
    {
      path: '/region-delivery/russian-post',
      name: 'Russian-post',
      component: RussianPost,
    },
    {
      path: '/user/favorites/',
      name: 'Favorites',
      component: Favorites,
    },
    {
      path: '/compare/',
      name: 'Compare',
      component: Compare,
    },
    {
      path: '/user/orders/all/',
      name: 'Crders',
      component: Orders,
    },
    {
      path: '/cart-checkout/',
      name: 'Cart-checkout',
      component: CartCheckout,
    },
    {
      path: '/product/productCard',
      name: 'ProductCard',
      component: ProductCard,
    },
  ]
})

export default router;
