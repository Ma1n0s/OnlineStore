<!-- <script setup>
import { ref, onMounted } from "vue";
import { Loader } from "@googlemaps/js-api-loader";

const map = ref(null);
const config = useRuntimeConfig();

onMounted(async () => {
  const loader = new Loader({
    // apiKey: config.public.env.GOOGLE_MAPS_API_KEY || "",
    version: "weekly",
  });

  await loader.load();

  new google.maps.Map(map.value, {
    center: { lat: 59.93428, lng: 30.335098 },
    zoom: 12,
  });
});
</script>
<template>
  <div ref="map" class="w-full h-96"></div>
</template> -->
