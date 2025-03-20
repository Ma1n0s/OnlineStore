// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  sourcemap: false,
  modules: [
    "@pinia/nuxt",
    "@nuxt/image",
    "@nuxtjs/sitemap",
    "@nuxtjs/seo",
    "@nuxtjs/robots",
    "@nuxt/icon",
    "@nuxtjs/tailwindcss",
  ],

  runtimeConfig: {
    public: {},
  },
});
