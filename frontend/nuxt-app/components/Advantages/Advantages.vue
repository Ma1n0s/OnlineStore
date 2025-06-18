<template>
  <div class="flex flex-row flex-wrap lg:flex-nowrap gap-4">
    <NuxtLink
      to="/news/1"
      class="min-h-36 lg:min-h-48 min-w-52 flex flex-1 lg:flex-none flex-col justify-between items-start gap-2 bg-zinc-200 py-4 px-4 rounded-2xl hover:bg-zinc-300 transition-colors cursor-pointer shadow-xl hover:shadow-2xl"
    >
      <div class="flex flex-col items-start gap-2">
        <Icon name="material-symbols:check-circle-outline-rounded" class="h-8 w-8" />
        <p class="text-base font-extrabold">Гарантия качества</p>
      </div>
      <p class="text-sm sm:text-base hover:text-primary transition-colors">Подробнее</p>
    </NuxtLink>

    <NuxtLink
      to="/news/2"
      class="min-h-36 lg:min-h-48 min-w-52 flex flex-1 lg:flex-none flex-col justify-between items-start gap-2 bg-zinc-200 py-4 px-4 rounded-2xl hover:bg-zinc-300 transition-colors cursor-pointer shadow-xl hover:shadow-2xl"
    >
      <div class="flex flex-col items-start gap-2">
        <Icon name="material-symbols:attach-money-rounded" class="h-8 w-8" />
        <p class="text-base font-extrabold">Система баллов</p>
      </div>
      <p class="text-sm sm:text-base hover:text-primary transition-colors">Подробнее</p>
    </NuxtLink>

    <NuxtLink
      to="/news/3"
      class="min-h-36 lg:min-h-48 min-w-52 flex flex-1 lg:flex-none flex-col justify-between items-start gap-2 bg-zinc-200 py-4 px-4 rounded-2xl hover:bg-zinc-300 transition-colors cursor-pointer shadow-xl hover:shadow-2xl"
    >
      <div class="flex flex-col items-start gap-2">
        <Icon name="material-symbols:calendar-clock-outline-rounded" class="h-8 w-8" />
        <p class="text-base font-extrabold">Аренда оборудования</p>
      </div>
      <p class="text-sm sm:text-base hover:text-primary transition-colors">Подробнее</p>
    </NuxtLink>

    <NuxtLink
      to="/news/4"
      class="bg-gradient-to-bl from-slate-900 to-slate-200 w-full min-h-36 lg:min-h-48 p-4 rounded-2xl transition-colors cursor-pointer shadow-xl hover:shadow-2xl flex flex-col gap-4"
    >
      <div class="text-white text-2xl font-bold">Покупайте как юрлицо</div>
      <div class="flex flex-row flex-wrap items-start gap-2">
        <div class="bg-white rounded-2xl p-2 px-4 text-black font-bold text-sm">Вычет НДС 20%</div>
        <div class="bg-white rounded-2xl p-2 px-4 text-black font-bold text-sm">ЭДО</div>
        <div class="bg-white rounded-2xl p-2 px-4 text-black font-bold text-sm">Персональный менеджер</div>
        <div class="bg-white rounded-2xl p-2 px-4 text-black font-bold text-sm">ИП и ООО</div>
      </div>
    </NuxtLink>
  </div>

  <!-- <div class="flex flex-col lg:flex-row items-center lg:flex-wrap justify-evenly gap-16 pt-8">
    <div class="flex flex-col gap-2 border-r border-primary w-full px-4 lg:w-1/3">
      <div class="flex items-center gap-2 text-lg sm:text-xl">
        <Icon name="material-symbols:check-circle-outline-rounded" class="h-6 w-6" />
        <p class="font-extrabold">Гарантия качества</p>
      </div>
      <p class="text-base sm:text-lg">Мы гарантируем качество нашей продукции и сервиса.</p>
    </div>

    <div class="flex flex-col gap-2 border-r border-primary w-full px-4 lg:w-1/3">
      <div class="flex items-center gap-2 text-lg sm:text-xl">
        <Icon name="material-symbols:star-half-rounded" class="h-6 w-6" />
        <p class="font-extrabold">Уникальные предложения</p>
      </div>
      <p class="text-base sm:text-lg">
        У нас есть уникальные товары, которые вы не найдете ни в одном другом магазине.
      </p>
    </div>

    <div class="flex flex-col gap-2 border-r border-primary w-full px-4 lg:w-1/3">
      <div class="flex items-center gap-2 text-lg sm:text-xl">
        <Icon name="material-symbols:passkey-rounded" class="h-6 w-6" />
        <p class="font-extrabold">Аренда оборудования</p>
      </div>
      <p class="text-base sm:text-lg">Аренда оборудования на выгодных условиях.</p>
    </div>

    <div class="flex flex-col gap-2 border-r border-primary w-full px-4 lg:w-1/3">
      <div class="flex items-center gap-2 text-lg sm:text-xl">
        <Icon name="material-symbols:check-circle-outline-rounded" class="h-6 w-6" />
        <p class="font-extrabold">Аренда оборудования</p>
      </div>
      <p class="text-base sm:text-lg">Аренда оборудования на выгодных условиях.</p>
    </div>
  </div> -->
</template>
