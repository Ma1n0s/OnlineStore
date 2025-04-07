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
  ],

  googleFonts: {
    families: {
      Montserrat: [400, 500, 600, 700, 800],
    },
    display: "swap",
  },

  runtimeConfig: {
    public: {},
  },

  app: {
    head: {
      link: [{ rel: "icon", type: "image/x-icon", href: "/logo_test.svg" }],
    },
  },
});