<template>
  <NuxtLayout>
    <div class="min-h-[50vh] flex flex-col">
      <NuxtPage />
      <MobileBottomNav />
    </div>
  </NuxtLayout>
</template>

<style>
body {
  font-family: 'Montserrat', sans-serif;
}
</style>

<script setup>
import MobileBottomNav from './components/MobileBottomNav/MobileBottomNav.vue'
import { defineOrganization, defineWebPage, defineWebSite, useSchemaOrg } from '@unhead/schema-org/vue'

// we can remove a lot of boilerplate from Schema.org by providing template params
const route = useRoute()

useHead({
  templateParams: {
    schemaOrg: {
      host: 'https://nuxtseo.com',
      path: route.path,
      inLanguage: 'en',
    },
  },

  script: [
    // Google Tag Manager
    {
      children: `
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-K3GWBBF2');
      `,
      type: 'text/javascript',
    },
    // Яндекс.Метрика
    {
      children: `
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
        m[i].l=1*new Date();
        for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
        k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
        
        ym(102976781, "init", {
          clickmap:true,
          trackLinks:true,
          accurateTrackBounce:true,
          webvisor:true
        });
      `,
      type: 'text/javascript',
    },
  ],
  noscript: [
    // Google Tag Manager (noscript)
    {
      children: `
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K3GWBBF2"
        height="0" width="0" style="display:none;visibility:hidden"></iframe>
      `,
      body: true,
    },
    // Яндекс.Метрика (noscript)
    {
      children: `
        <div>
          <img src="https://mc.yandex.ru/watch/102976781" style="position:absolute; left:-9999px;" alt="" />
        </div>
      `,
      body: true,
    },
  ],
})

useSchemaOrg([
  // much of the data will be inferred such as the title, description and all URLs
  defineWebPage(),
  defineWebSite({
    name: 'Nuxt SEO',
    description: 'Nuxt SEO is a collection of hand-crafted Nuxt Modules to help you rank higher in search engines.',
  }),
  // choose an identity, either with definePerson or an defineOrganization
  defineOrganization({
    name: 'Nuxt SEO',
  }),
])
</script>
