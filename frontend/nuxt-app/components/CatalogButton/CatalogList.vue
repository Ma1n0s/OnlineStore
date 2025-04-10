<template>
  <div class="absolute mt-1 z-10 flex bg-white border border-primary-hover text-dark rounded-xl p-1 max-h-96 h-[96rem]">
    <div class="h-full flex flex-col p-2 border-r border-primary">
      <div
        v-for="(item, index) in catalog"
        :key="index"
        :class="[
          '~p-4/2 cursor-pointer box-border border-b border-transparent hover:border-dark overflow-auto',
          { '!border-dark': item.name === selectedCategory?.name },
        ]"
        @mouseenter="toggleSelectedCategory(item)"
      >
        {{ item.name }}
      </div>
    </div>
    <div v-show="!!selectedCategory" class="grid grid-cols-2 xl:grid-cols-3 p-2 xl:max-w-[800px] w-max overflow-auto">
      <div
        v-for="(elements, index) in selectedCategory?.elements"
        :key="`${index}-catalog`"
        class="~p-4/2 h-fit cursor-pointer hover:bg-dark/20"
      >
        {{ elements?.name || 'Error' }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { target } = defineProps<{
  target: HTMLElement
}>()

type CatalogItem = { name: string; elements: any[] }

const catalog: CatalogItem[] = [
  {
    name: 'Инструменты',
    elements: [
      { name: 'Шуруповерты' },
      { name: 'Дрели' },
      { name: 'Болгарки' },
      { name: 'Бороздоделы' },
      { name: 'Гайковерты' },
      { name: 'Гвоздезабиватели' },
      { name: 'Дрели' },
      { name: 'Граверы' },
      { name: 'Заклепочники' },
      { name: 'Измерительный инструмент' },
      { name: 'Лабораторное оборудование' },
      { name: 'Лобзики' },
      { name: 'Ножницы по металлу' },
      { name: 'Отбойные молотки' },
      { name: 'Паяльное оборудование' },
      { name: 'Перфораторы' },
      { name: 'Пилы' },
      { name: 'Пистолеты' },
      { name: 'Пневмоинструмент' },
      { name: 'Пневмошуруповерты' },
      { name: 'Сварочное оборудование' },
      { name: 'Строительные пылесосы' },
      { name: 'Шлифмашинки' },
      { name: 'Электрорубанки' },
    ],
  },
  { name: 'Другое', elements: [{ name: '1' }, { name: '2' }] },
]

const selectedCategory = ref<CatalogItem>(catalog[0] || null)
const toggleSelectedCategory = (item: CatalogItem) => {
  selectedCategory.value = item
}

const showMenu = defineModel<boolean>('showMenu', { required: true })

onClickOutside(target, () => {
  showMenu.value = false
})
</script>
