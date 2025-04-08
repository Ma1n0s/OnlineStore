// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: {
    enabled: true,

    timeline: {
      enabled: true,
    },
  },
  sourcemap: false,
  modules: [
    "@pinia/nuxt",
    "@nuxt/image",
    "@nuxtjs/sitemap",
    "@nuxtjs/seo",
    "@nuxtjs/robots",
    "@nuxt/icon",
    "@nuxtjs/tailwindcss",
    "@vueuse/nuxt",
    "nuxt-swiper",
    "@nuxtjs/google-fonts",
    "nuxt-auth-sanctum",
  ],

  googleFonts: {
    families: {
      Montserrat: [400, 500, 600, 700, 800],
    },
    display: "swap",
  },

  runtimeConfig: {
    public: {
      sanctum: {
        baseUrl: import.meta.env.VITE_BACKEND || "http://localhost:8000", // Laravel API
        mode: "cookie",
        userStateKey: "sanctum.user.identity",
        redirectIfAuthenticated: true,
        redirectIfUnauthenticated: true,
        endpoints: {
          csrf: "/sanctum/csrf-cookie",
          login: "/login",
          logout: "/logout",
        },
        csrf: {
          cookie: "XSRF-TOKEN",
          header: "X-XSRF-TOKEN",
        },
      },
    },
  },

  app: {
    head: {
      title: "Строительный магазин Абсолют техно",
      htmlAttrs: {
        lang: "ru",
      },
      link: [{ rel: "icon", type: "image/x-icon", href: "/logo_test.svg" }],
    },
  },
});
