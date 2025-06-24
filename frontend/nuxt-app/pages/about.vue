<script setup>
useHead({
  title: `О компании Абсолют техно`,
  meta: [
    {
      name: 'description',
      content: `Инструменты и оборудование для строительства и ремота`,
    },
  ],
})
</script>

<template>
  <div class="bg-white w-full mt-12 p-2 ~pb-4/8">
    <div class="max-w-screen-2xl mx-auto px-1 sm:px-2 lg:px-8">
      <div class="mb-16">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
          <div class="lg:w-1/2">
            <h2 class="text-3xl font-bold mb-6">О компании Абсолют техно</h2>
            <div class="space-y-4 text-gray-700 leading-relaxed">
              <p>
                <strong>Абсолют техно</strong> - ведущий поставщик профессионального инструмента, строительного
                оборудования и промышленных решений в России. С 2010 года мы помогаем профессионалам и компаниям
                находить оптимальные технические решения для реализации любых проектов.
              </p>
              <p>
                Наша компания объединяет многолетний опыт, глубокие отраслевые знания и стремление к инновациям,
                предлагая клиентам только проверенное и качественное оборудование от мировых производителей.
              </p>
              <p>
                Сегодня Абсолют техно - это не просто магазин инструментов, а полноценный технологический партнер,
                готовый предложить комплексные решения для строительства, производства и ремонтных работ любого
                масштаба.
              </p>
            </div>
          </div>
          <div class="lg:w-1/2 bg-gray-100 rounded-xl overflow-hidden">
            <NuxtImg
              src="full_logo_text.svg"
              alt="Шоурум Абсолют техно"
              class="w-full h-auto object-cover"
              loading="lazy"
            />
          </div>
        </div>
      </div>

      <div class="mb-16">
        <h2 class="text-3xl font-bold mb-8 text-center">Наши ценности</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="p-8 rounded-xl shadow-md border-t-4 border-second">
            <div class="text-second text-4xl mb-4">✓</div>
            <h3 class="text-xl font-bold mb-3">Профессионализм</h3>
            <p class="text-gray-600">
              Мы тщательно отбираем ассортимент, предлагая только проверенные решения. Наши специалисты регулярно
              проходят обучение у производителей, чтобы предоставлять клиентам экспертную консультацию.
            </p>
          </div>
          <div class="p-8 rounded-xl shadow-md border-t-4 border-second">
            <div class="text-second text-4xl mb-4">✓</div>
            <h3 class="text-xl font-bold mb-3">Надежность</h3>
            <p class="text-gray-600">
              Гарантия качества на все товары, официальные поставки от производителей, прозрачные условия
              сотрудничества. Мы дорожим своей репутацией и доверием клиентов.
            </p>
          </div>
          <div class="p-8 rounded-xl shadow-md border-t-4 border-second">
            <div class="text-second text-4xl mb-4">✓</div>
            <h3 class="text-xl font-bold mb-3">Инновации</h3>
            <p class="text-gray-600">
              Мы постоянно следим за тенденциями рынка, внедряем новые технологии и предлагаем клиентам современные
              решения, повышающие эффективность их работы.
            </p>
          </div>
        </div>
      </div>

      <!-- <div class="mb-16 bg-gray-50 py-12 rounded-xl">
        <h2 class="text-3xl font-bold mb-12 text-center">Абсолют техно в цифрах</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-5xl mx-auto">
          <div class="text-center">
            <div class="text-second text-5xl font-bold mb-2">13+</div>
            <p class="text-gray-600 font-medium">Лет успешной работы</p>
          </div>
          <div class="text-center">
            <div class="text-second text-5xl font-bold mb-2">10 000+</div>
            <p class="text-gray-600 font-medium">Наименований в каталоге</p>
          </div>
          <div class="text-center">
            <div class="text-second text-5xl font-bold mb-2">500+</div>
            <p class="text-gray-600 font-medium">Корпоративных клиентов</p>
          </div>
          <div class="text-center">
            <div class="text-second text-5xl font-bold mb-2">24/7</div>
            <p class="text-gray-600 font-medium">Поддержка клиентов</p>
          </div>
        </div>
      </div>

      <div class="mb-16">
        <h2 class="text-3xl font-bold mb-8 text-center">Почему выбирают нас</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Широкий ассортимент</h3>
            </div>
            <p class="text-gray-600">
              От ручного инструмента до промышленного оборудования. В нашем каталоге представлено более 10 000
              наименований от ведущих мировых брендов и проверенных отечественных производителей.
            </p>
          </div>
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Гарантия качества</h3>
            </div>
            <p class="text-gray-600">
              Все товары имеют сертификаты соответствия и гарантию производителя. Мы тщательно проверяем поставщиков и
              предлагаем только надежное оборудование с подтвержденными характеристиками.
            </p>
          </div>
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Техническая поддержка</h3>
            </div>
            <p class="text-gray-600">
              Наши инженеры и технические специалисты готовы помочь с выбором оборудования, его настройкой и
              обслуживанием. Консультации на русском языке от профессионалов отрасли.
            </p>
          </div>
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Гибкие условия</h3>
            </div>
            <p class="text-gray-600">
              Индивидуальный подход к каждому клиенту. Специальные условия для корпоративных заказчиков, программы
              лояльности, различные варианты оплаты и доставки по всей России.
            </p>
          </div>
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Складская программа</h3>
            </div>
            <p class="text-gray-600">
              Собственные склады в ключевых регионах позволяют оперативно отгружать популярные позиции. Более 70%
              ассортимента доступно для отгрузки в день заказа.
            </p>
          </div>
          <div class="p-6 rounded-lg border border-gray-200 hover:shadow-lg transition-shadow">
            <div class="flex items-start mb-4">
              <div class="p-2 rounded-full mr-4">
                <Icon name="material-symbols:check-rounded" class="w-8 h-8" />
              </div>
              <h3 class="text-xl font-semibold">Обучение и сервис</h3>
            </div>
            <p class="text-gray-600">
              Проводим обучение работе с оборудованием, организуем демонстрации техники. Собственный сервисный центр
              осуществляет гарантийное и постгарантийное обслуживание.
            </p>
          </div>
        </div>
      </div>

      <div class="mb-16">
        <h2 class="text-3xl font-bold mb-8 text-center">Профессиональная команда</h2>
        <p class="text-gray-600 max-w-3xl mx-auto text-center mb-12">
          В Абсолют техно работают высококвалифицированные специалисты с многолетним опытом в строительной и
          промышленной отрасли. Мы объединяем экспертов разных направлений для комплексного обслуживания клиентов.
        </p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div class="rounded-lg overflow-hidden shadow-md">
            <NuxtImg src="88.1.jpg" alt="Иван Петров" class="w-full h-64 object-cover" />
            <div class="p-6">
              <h3 class="text-xl font-bold mb-1">Иван Петров</h3>
              <p class="text-second font-medium mb-3">Генеральный директор</p>
              <p class="text-gray-600 text-sm">
                Основатель компании, эксперт в области промышленного оборудования с 20-летним стажем.
              </p>
            </div>
          </div>
          <div class="rounded-lg overflow-hidden shadow-md">
            <NuxtImg src="88.1.jpg" alt="Александра Смирнова" class="w-full h-64 object-cover" />
            <div class="p-6">
              <h3 class="text-xl font-bold mb-1">Александра Смирнова</h3>
              <p class="text-second font-medium mb-3">Технический директор</p>
              <p class="text-gray-600 text-sm">
                Инженер-механик, курирует подбор оборудования и техническую поддержку клиентов.
              </p>
            </div>
          </div>
          <div class="rounded-lg overflow-hidden shadow-md">
            <NuxtImg src="88.1.jpg" alt="Дмитрий Волков" class="w-full h-64 object-cover" />
            <div class="p-6">
              <h3 class="text-xl font-bold mb-1">Дмитрий Волков</h3>
              <p class="text-second font-medium mb-3">Руководитель отдела продаж</p>
              <p class="text-gray-600 text-sm">Специалист по работе с корпоративными клиентами</p>
            </div>
          </div>
          <div class="rounded-lg overflow-hidden shadow-md">
            <NuxtImg src="88.1.jpg" alt="Екатерина Козлова" class="w-full h-64 object-cover" />
            <div class="p-6">
              <h3 class="text-xl font-bold mb-1">Екатерина Козлова</h3>
              <p class="text-second font-medium mb-3">Менеджер по маркетингу</p>
              <p class="text-gray-600 text-sm">Организатор отраслевых мероприятий.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="text-black py-12 px-8 rounded-xl">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="text-3xl font-bold mb-6">Готовы сотрудничать с Абсолют техно?</h2>
          <p class="text-xl mb-8">
            Наши специалисты помогут подобрать оборудование для ваших задач и предложат оптимальные условия
            сотрудничества.
          </p>
          <div class="flex flex-col sm:flex-row justify-center gap-4">
            <button class="text-second font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">
              Оставить заявку
            </button>
            <button
              class="border-2 border-black text-black font-bold py-3 px-8 rounded-lg hover: hover:bg-opacity-10 transition"
            >
              Заказать звонок
            </button>
          </div>
        </div>
      </div> -->
    </div>
  </div>
</template>
