import './assets/main.css';

import { createApp } from 'vue';
import { createYmaps } from 'vue-yandex-maps';
import App from './App.vue';
import router from './router';
import './assets/tailwind.css';

const app = createApp(App);

app.use(createYmaps({
    apikey: '2d681ad7-e82e-47e4-97d2-8198a44c425b',
}));

app.use(router);
app.mount('#app');