<script setup lang="ts">
useHead({
  title: 'О нас | Абсолют техно',
  meta: [
    {
      name: 'description',
      content:
        'Абсолют техно - ведущий поставщик строительных инструментов и товаров с 2023 года. Узнайте о нашей истории, миссии, команде и преимуществах для клиентов. Более 10 тысяч товаров в ассортименте.',
    },
  ],
})
</script>
<template>
  <div class="flex mb-2">
    <div class="bg-primary text-white w-10 h-10 ml-2 flex items-center justify-center">1</div>
    <div class="bg-second text-white w-10 h-10 ml-2 flex items-center justify-center">2</div>
    <div class="bg-danger text-white w-10 h-10 ml-2 flex items-center justify-center">3</div>
    <div class="bg-white text-black w-10 h-10 ml-2 flex items-center justify-center">4</div>
    <div class="bg-danger text-white w-10 h-10 ml-2 flex items-center justify-center">5</div>
    <div class="bg-dark text-white w-10 h-10 ml-2 flex items-center justify-center">6</div>
    <div class="bg-gray text-white w-10 h-10 ml-2 flex items-center justify-center">7</div>
  </div>

  <div class="bg-white text-dark min-h-screen">
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <!-- Наша история -->
      <section class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Наша история</h2>
        <p class="text-gray-700 leading-relaxed">
          Мы начали свой путь в 2010 году с небольшого магазина инструментов в центре города. С тех пор мы выросли в
          крупную сеть, предлагающую широкий ассортимент качественных инструментов для профессионалов и любителей.
        </p>
      </section>

      <!-- Наша миссия -->
      <section class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Наша миссия</h2>
        <p class="text-gray-700 leading-relaxed">
          Наша миссия - предоставлять нашим клиентам лучшие инструменты по доступным ценам, помогая им воплощать свои
          проекты в жизнь.
        </p>
      </section>

      <!-- О нас в цифрах -->
      <section class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">О нас в цифрах</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-second text-white p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-3xl font-bold mb-2">10+</h3>
            <p class="text-gray-200">Лет на рынке</p>
          </div>
          <div class="bg-second text-white p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-3xl font-bold mb-2">1.7M+</h3>
            <p class="text-gray-200">Товаров на сайте</p>
          </div>
          <div class="bg-second text-white p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-3xl font-bold mb-2">500+</h3>
            <p class="text-gray-200">Довольных клиентов</p>
          </div>
          <div class="bg-second text-white p-6 rounded-lg shadow-lg text-center">
            <h3 class="text-3xl font-bold mb-2">50+</h3>
            <p class="text-gray-200">Сотрудников в команде</p>
          </div>
        </div>
      </section>

      <!-- Польза для клиентов -->
      <section class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Польза для клиентов</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">Широкий ассортимент</h3>
            <p class="text-gray-700">
              Более 1,7 млн товаров на сайте доступны для заказа. Мы предлагаем инструменты для любых задач.
            </p>
          </div>
          <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">Качество и надежность</h3>
            <p class="text-gray-700">
              Все товары проходят строгий контроль качества. Мы работаем только с проверенными поставщиками.
            </p>
          </div>
          <div class="bg-white border border-gray-200 p-6 rounded-lg shadow-sm">
            <h3 class="text-xl font-semibold mb-2">Удобство и поддержка</h3>
            <p class="text-gray-700">Быстрая доставка, удобная оплата и профессиональная поддержка 24/7.</p>
          </div>
        </div>
      </section>

      <!-- Наша команда -->
      <section class="mb-8">
        <h2 class="text-2xl font-semibold mb-4">Наша команда</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="bg-second text-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-2">Иван Иванов</h3>
            <p class="text-gray-200">Основатель и CEO</p>
          </div>
          <div class="bg-second text-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-2">Мария Петрова</h3>
            <p class="text-gray-200">Менеджер по продажам</p>
          </div>
          <div class="bg-second text-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-2">Алексей Сидоров</h3>
            <p class="text-gray-200">Технический специалист</p>
          </div>
        </div>
      </section>

      <section></section>
    </main>
  </div>
</template>
