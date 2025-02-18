<template>
    <yandex-map
      v-model="map"
      :settings="mapState"
      width="100%"
      height="500px"
    >
      <yandex-map-default-scheme-layer />
      <yandex-map-default-features-layer />
      <yandex-map-default-marker :settings="{ coordinates: markerCoords }" />
    </yandex-map>
  </template>
  
  <script setup lang="ts">
  import { ref, shallowRef } from 'vue';
  import { YMap } from '@yandex/ymaps3-types';
  
  const map = shallowRef<null | YMap>(null);
  
  const mapState = ref({
    center: [55.751574, 37.573856], 
    zoom: 9, 
  });
  
  const markerCoords = ref([55.751574, 37.573856]);
  </script>