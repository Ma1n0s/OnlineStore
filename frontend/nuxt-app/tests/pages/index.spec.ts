import { describe, it, expect, vi } from 'vitest'
import { shallowMount } from '@vue/test-utils'
import IndexPage from '~/pages/index.vue'

// Мокаем компоненты, используемые на странице
vi.mock('~/components/Swiper/BrandSwiper.vue', () => ({
  default: {
    name: 'BrandSwiper',
    template: '<div data-testid="brand-swiper">BrandSwiper</div>',
  },
}))

vi.mock('~/components/Swiper/CategorySwiper.vue', () => ({
  default: {
    name: 'CategorySwiper',
    template: '<div data-testid="category-swiper">CategorySwiper</div>',
  },
}))

vi.mock('~/components/Swiper/ProductSwiper/ProductSwiper.vue', () => ({
  default: {
    name: 'ProductSwiper',
    template: '<div data-testid="product-swiper">ProductSwiper</div>',
  },
}))

describe('Index Page', () => {
  it('рендерится корректно', () => {
    const wrapper = shallowMount(IndexPage)
    expect(wrapper.exists()).toBe(true)
  })

  it('содержит все требуемые компоненты слайдеров', () => {
    const wrapper = shallowMount(IndexPage)

    expect(wrapper.findComponent({ name: 'BrandSwiper' }).exists()).toBe(true)
    expect(wrapper.findComponent({ name: 'CategorySwiper' }).exists()).toBe(true)
    expect(wrapper.findComponent({ name: 'ProductSwiper' }).exists()).toBe(true)
  })
})
