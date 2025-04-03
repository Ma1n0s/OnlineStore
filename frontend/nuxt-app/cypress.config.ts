import { defineConfig } from "cypress";
import viteConfig from "./vite.config";

export default defineConfig({
  e2e: {
    baseUrl: "http://localhost:3000",
    supportFile: "cypress/support/e2e.ts",
    specPattern: "cypress/e2e/**/*.cy.{js,ts,vue}",
    viewportWidth: 1280,
    viewportHeight: 720,
    video: true,
  },
  component: {
    devServer: {
      framework: "vue",
      bundler: "vite",
      viteConfig,
    },
    supportFile: "cypress/support/component.ts",
    specPattern: "cypress/component/**/*.cy.{js,ts,vue}",
    indexHtmlFile: "cypress/support/component-index.html",
  },
});
