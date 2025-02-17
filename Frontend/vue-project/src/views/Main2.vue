<script setup>
import { ref, computed  }  from "vue";

  const currentIndex = ref(0);
  const currentIndex2 = ref(0);
  const groups = ref([]);

  const slides = ref([
    { title: "Slide 1", image: "https://via.placeholder.com/600x300?text=Slide+1" },
    { title: "Slide 2", image: "https://via.placeholder.com/600x300?text=Slide+2" },
    { title: "Slide 3", image: "https://via.placeholder.com/600x300?text=Slide+3" },
  ])
  const slides2 = ref([
    { title: "Slide 1", image: "https://via.placeholder.com/600x300?text=Slide+1" },
    { title: "Slide 2", image: "https://via.placeholder.com/600x300?text=Slide+2" },
    { title: "Slide 3", image: "https://via.placeholder.com/600x300?text=Slide+3" },
    { title: "Slide 4", image: "https://via.placeholder.com/600x300?text=Slide+4" },
    { title: "Slide 5", image: "https://via.placeholder.com/600x300?text=Slide+5" },
    { title: "Slide 6", image: "https://via.placeholder.com/600x300?text=Slide+6" },
  ])
  function nextSlide() {
    currentIndex.value = (currentIndex.value + 1) % slides.value.length;
  }
  function prevSlide() {
    currentIndex.value = (currentIndex.value - 1 + slides.value.length) % slides.value.length;
  }
  function nextSlide2() {
      if (currentIndex2.value < groupedSlides.value.length - 1) {
        currentIndex2.value++;
      } else {
        currentIndex2.value = 0; 
      }
  }
  function prevSlide2() {
    if (currentIndex2.value > 0) {
        currentIndex2.value--;
      } else {
        currentIndex.value = groupedSlides.value.length - 1;
      }
  }

  const groupedSlides = computed(() => {
    return Array.from({ length: Math.ceil(slides2.value.length / 2) }, (_, i) => 
      slides2.value.slice(i * 3, i * 3 + 3)
    );
  });

  // onMounted(() => {
  //   setInterval(nextSlide, 3000)
  // });


</script>
<template>
<div class="px-52">
    <div class="relative w-full px-52 mx-auto mb-4">
      <div
        class="overflow-hidden rounded-lg shadow-lg"
      >
        <div
          class="flex transition-transform duration-300"
          :style="{ transform: `translateX(-${currentIndex * 100}%)` }"
        >
          <div
            v-for="(slide, index) in slides"
            :key="index"
            class="min-w-full"
          >
            <img :src="slide.image" :alt="slide.title" class="w-full h-auto" />
            <h2 class="text-center text-xl font-bold">{{ slide.title }}</h2>
          </div>
        </div>
      </div>
  
      <button
        @click="prevSlide"
        class="absolute top-1/2 left-4 transform -translate-y-1/2 text-white bg-black rounded-full w-8 h-8 flex items-center justify-center p-2"
      >
        &#10094;
      </button>

      <button
        @click="nextSlide"
        class="absolute top-1/2 right-4 transform -translate-y-1/2 text-white bg-black rounded-full w-8 h-8 flex items-center justify-center p-2"
      >
        &#10095;
      </button>
    </div>

 <div class="relative w-full max-w-lg mx-auto">
    <div class="overflow-hidden rounded-lg shadow-lg">
      <div
        class="flex transition-transform duration-300"
        :style="{ transform: `translateX(-${currentIndex2 * (100 / 3)}%)` }"
      >
        <div
          v-for="(slideGroup, index) in groupedSlides"
          :key="index"
          class="flex min-w-full"
        >
          <div
            v-for="(slide, slideIndex) in slideGroup"
            :key="slideIndex"
            class="w-1/3 p-2"
          >
            <img :src="slide.image" :alt="slide.title" class="w-full h-auto rounded-lg" />
            <h2 class="text-center text-xl font-bold">{{ slides2.title }}</h2>
          </div>
        </div>
      </div>
    </div>
    
    <button @click="prevSlide2"  class="absolute top-1/2 left-4 transform -translate-y-1/2 text-white bg-black rounded-full w-8 h-8 flex items-center justify-center p-2">
      &#10094;
    </button>
    <button @click="nextSlide2" class="absolute top-1/2 right-4 transform -translate-y-1/2 text-white bg-black rounded-full w-8 h-8 flex items-center justify-center p-2">
      &#10095;
    </button>
  </div>


    <h1 class="font-bold text-2xl mb-4">ВсеИнструменты.ру – ваш надежный поставщик</h1>
    <div class="flex space-x-8">
      <div class="shadow-lg h-52 w-52 rounded-lg flex flex-col">
          <div class="flex items-start">
              <img src="/public/box.svg" alt="box" class="w-10 h-10  m-4" />
          </div>
        <div class="flex-grow flex items-center justify-center">
              <div class="text-start ml-4">
                  1 500 000 товаров в наличии
              </div>
          </div>
          <div class="text-gray-600 hover:text-rose-700 text-left ml-4 mb-2">
              Подробнее
        </div>
      </div>
      <div class="shadow-lg h-52 w-52 rounded-lg flex flex-col">
          <div class="flex items-start">
              <img src="/public/box.svg" alt="box" class="w-10 h-10  m-4" />
          </div>
        <div class="flex-grow flex items-center justify-center">
              <div class="text-start ml-4">
                  1 500 000 товаров в наличии
              </div>
          </div>
          <div class="text-gray-600 hover:text-rose-700 text-left ml-4 mb-2">
              Подробнее
        </div>
      </div>
      <div class="shadow-lg h-52 w-52 rounded-lg flex flex-col">
          <div class="flex items-start">
              <img src="/public/box.svg" alt="box" class="w-10 h-10  m-4" />
          </div>
        <div class="flex-grow flex items-center justify-center">
              <div class="text-start ml-4">
                  1 500 000 товаров в наличии
              </div>
          </div>
          <div class="text-gray-600 hover:text-rose-700 text-left ml-4 mb-2">
              Подробнее
        </div>
      </div>
        <div>
          <img src="/public/8c696bc17de6aed2f1015d3612934a6b.jpeg" alt="logo" class="rounded-xl"/>
        </div>
    </div>
</div>
  </template>
  
  
  
  <style scoped>
  button {
    transition: background-color 0.3s;
  }
  
  button:hover {
    background-color: rgba(0, 0, 0, 0.7);
  }
  </style>
  