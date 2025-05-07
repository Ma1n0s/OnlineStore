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

  // Конфигурация для Iconify, чтобы решить проблему с загрузкой иконок
  icon: {
    // Добавляем кэширование иконок на стороне клиента
    clientBundle: {
      scan: true,
      sizeLimitKb: 256,
    },
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

  // Добавляем правила маршрутизации для API
  routeRules: {
    '/api/**': { proxy: { to: process.env.VITE_BACKEND || 'http://127.0.0.1:8000' } },
    '/sanctum/**': { proxy: { to: process.env.VITE_BACKEND || 'http://127.0.0.1:8000' } },
  },

  googleFonts: {
    families: {
      Montserrat: [400, 500, 600, 700, 800],
    },
    display: 'swap',
  },

  runtimeConfig: {
    public: {
      backendUrl: process.env.VITE_BACKEND || 'http://127.0.0.1:8000',
      sanctum: {
        baseUrl: process.env.VITE_BACKEND || 'http://127.0.0.1:8000',
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
      crawlLinks: true, // Для автоматического обнаружения ссылок
    },
    // Настраиваем проксирование
    devProxy: {
      '/api/**': {
        target: process.env.VITE_BACKEND || 'http://127.0.0.1:8000',
        changeOrigin: true,
        cookieDomainRewrite: {
          '*': '',
        },
        headers: {
          'X-Forwarded-Host': '127.0.0.1:3000',
          'X-Forwarded-Proto': 'https', // Изменяем на https
          Origin: 'http://127.0.0.1:3000',
        },
        secure: true, // Разрешаем безопасное соединение
        xfwd: true,
      },
      '/sanctum/**': {
        target: process.env.VITE_BACKEND || 'http://127.0.0.1:8000',
        changeOrigin: true,
        cookieDomainRewrite: {
          '*': '',
        },
        headers: {
          'X-Forwarded-Host': '127.0.0.1:3000',
          'X-Forwarded-Proto': 'https', // Изменяем на https
          Origin: 'http://127.0.0.1:3000',
        },
        secure: true, // Разрешаем безопасное соединение
        xfwd: true,
      },
    },
  },

  // Настройка cookie для SSR
  ssr: true,

  // Глобальные настройки для HTTP
  experimental: {
    // Включаем экспериментальные функции для поддержки безопасных cookie
    cookieStore: true,
  },

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
