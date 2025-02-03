<script setup>
import { computed } from 'vue';
import icons from '@/icons';

const props = defineProps(['name']);

const path = computed(() => icons[props.name]);

</script>


<template>
    <svg 
    width="64"
     height="64"
      viewBox="0 0 64 64" 
      fill="none" 
      xmlns="http://www.w3.org/2000/svg"
      v-html="path"
      >
    </svg>

</template>