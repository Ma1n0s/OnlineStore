// https://nuxt.com/docs/api/configuration/nuxt-config
import tailwindcss from "@tailwindcss/vite";

export default defineNuxtConfig({
  compatibilityDate: "2024-11-01",
  devtools: { enabled: true },
  modules: ["@pinia/nuxt"],
  sourcemap: false,

  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  css: ["~/assets/css/tailwind.css"],
  // postcss: {
  //   plugins: {
  //     '@tailwindcss/postcss': {},
  //     autoprefixer: {},
  //   },
  // },

  runtimeConfig: {
    public: {
      googleMapsApiKey: process.env.GOOGLE_MAPS_API_KEY,
    },
  },
});
