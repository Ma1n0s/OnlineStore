// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: {
    enabled: true,

    timeline: {
      enabled: true,
    },
  },
  sourcemap: false,
  modules: [
    '@pinia/nuxt',
    '@nuxt/image',
    '@nuxtjs/sitemap',
    '@nuxtjs/seo',
    '@nuxtjs/robots',
    '@nuxt/icon',
    '@nuxtjs/tailwindcss',
    '@vueuse/nuxt',
    'nuxt-swiper',
    '@nuxtjs/google-fonts',
    'nuxt-auth-sanctum',
    '@samk-dev/nuxt-vcalendar',
    'nuxt-aos',
    // '@nuxtjs/proxy',
  ],

  icon: {
    provider: 'iconify',
  },

  // routeRules: {
  //   // Статические страницы с перегенерацией каждые 60 сек
  //   '/category': { isr: 3600 },
  //   '/category/**': { isr: 3600 },
  //   '/products/**': { isr: 3600 },
  //   '/products/category': { isr: 3600 },
  //   '/products/category/**': { isr: 3600 },
  //   '/products': { isr: 3600 },
  //   '/': { isr: 3600 },
  // },

  routeRules: {
    '/products/**': { isr: 60 },
  },

  googleFonts: {
    families: {
      Montserrat: [400, 500, 600, 700, 800],
    },
    display: 'swap',
  },

  runtimeConfig: {
    public: {
      backendUrl: process.env.BACKEND_URL || 'http://localhost:8000',
      sanctum: {
        baseUrl: process.env.BACKEND_URL || 'http://localhost:8000',
        mode: 'cookie',
        userStateKey: 'sanctum.user.identity',
        endpoints: {
          csrf: '/sanctum/csrf-cookie',
          login: '/api/auth/verify-code',
          logout: '/api/auth/logout',
          user: '/api/user',
        },
        csrf: {
          cookie: 'XSRF-TOKEN',
          header: 'X-XSRF-TOKEN',
        },
        redirect: {
          onLogin: '/',
          onLogout: '/',
          home: '/',
        },
        credentials: 'include',
      },
    },
  },

  // Добавляем настройки для axios и fetch
  nitro: {
    prerender: {
      crawlLinks: true,
    },
    devProxy: {
      '/api': {
        target: process.env.BACKEND_URL || 'http://localhost:8000',
        changeOrigin: true,
        cookieDomainRewrite: {
          '*': '',
        },
        headers: {
          'X-Forwarded-Host': 'localhost:3000',
          'X-Forwarded-Proto': 'http',
        },
        secure: false,
        // bypass: req => {
        //   // Полный путь без домена
        //   const fullPath = req.url || ''

        //   // Разделяем путь и query-параметры
        //   const [path] = fullPath.split('?')

        //   // Маршруты, которые должны обрабатываться локально
        //   const localRoutes = [
        //     '/api/search', // Будет обработан server/api/search.ts
        //     // '/api/another-local-route',
        //   ]

        //   // Проверяем совпадение с локальными маршрутами
        //   if (localRoutes.some(route => path === route)) {
        //     console.log(`[PROXY] Bypassing: ${fullPath} -> local Nitro handler`)
        //     return fullPath // Возвращаем оригинальный URL
        //   }

        //   console.log(`[PROXY] Forwarding: ${fullPath} -> ${this.target}`)
        //   return null // Продолжаем проксирование
        // },
      },
      '/sanctum': {
        target: process.env.BACKEND_URL || 'http://localhost:8000',
        changeOrigin: true,
        cookieDomainRewrite: {
          '*': '',
        },
        secure: false,
      },
    },
  },

  // Настройка cookie для SSR
  ssr: true,

  app: {
    head: {
      title: 'Строительный магазин Абсолют техно',
      htmlAttrs: {
        lang: 'ru',
      },
      link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
    },
  },
})
