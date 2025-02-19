import './assets/main.css';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { loadGoogleMaps } from '@fawmi/vue-google-maps';
import './assets/tailwind.css';

const app = createApp(App);

app.use(loadGoogleMaps, {
    load: {
      key: 'YOUR_API_KEY',
      libraries: 'places', 
    },
  });

app.use(router);
app.mount('#app');