<template>
    <div class="relative w-full max-w-lg mx-auto">
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
        class="absolute top-1/2 left-4 transform -translate-y-1/2 text-white bg-black rounded-full p-2"
      >
        &#10094;
      </button>
      <button
        @click="nextSlide"
        class="absolute top-1/2 right-4 transform -translate-y-1/2 text-white bg-black rounded-full p-2"
      >
        &#10095;
      </button>
    </div>

    <div>
        <h1>ВсеИнструменты.ру – ваш надежный поставщик</h1>
        <div class="border-2 shadow-sm h-52 w-52">
        <img src="" alt="box" class="" />
        <div>
            1 500 000 товаров в наличии
        </div>
        <div>
           Подробнее
        </div>
        </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        currentIndex: 0,
        slides: [
          { title: "Slide 1", image: "https://via.placeholder.com/600x300?text=Slide+1" },
          { title: "Slide 2", image: "https://via.placeholder.com/600x300?text=Slide+2" },
          { title: "Slide 3", image: "https://via.placeholder.com/600x300?text=Slide+3" },
        ],
      };
    },
    methods: {
      nextSlide() {
        this.currentIndex = (this.currentIndex + 1) % this.slides.length;
      },
      prevSlide() {
        this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
      },
    },
  };
  </script>
  
  <style scoped>
  button {
    transition: background-color 0.3s;
  }
  
  button:hover {
    background-color: rgba(0, 0, 0, 0.7);
  }
  </style>
  