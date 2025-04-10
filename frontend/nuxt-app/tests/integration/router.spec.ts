import { describe, it, expect, vi, beforeEach } from 'vitest'
import { config, mount } from '@vue/test-utils'
import { createRouter, createWebHistory } from 'vue-router'
import IndexPage from '~/pages/index.vue'
import AboutPage from '~/pages/about.vue'
import ContactsPage from '~/pages/contacts.vue'

// Мокаем NuxtImg компонент
config.global.stubs = {
  NuxtImg: true,
  NuxtLink: true,
}

// Мокаем все компоненты, используемые на страницах
vi.mock('~/components/Swiper/BrandSwiper.vue', () => ({
  default: { template: '<div>BrandSwiper</div>' },
}))

vi.mock('~/components/Swiper/CategorySwiper.vue', () => ({
  default: { template: '<div>CategorySwiper</div>' },
}))

vi.mock('~/components/Swiper/ProductSwiper/ProductSwiper.vue', () => ({
  default: { template: '<div>ProductSwiper</div>' },
}))

describe('Маршрутизация', () => {
  let router: any

  beforeEach(() => {
    router = createRouter({
      history: createWebHistory(),
      routes: [
        { path: '/', component: IndexPage },
        { path: '/about', component: AboutPage },
        { path: '/contacts', component: ContactsPage },
      ],
    })
  })

  it('переходит на домашнюю страницу', async () => {
    router.push('/')
    await router.isReady()

    const wrapper = mount(
      {
        template: '<router-view></router-view>',
      },
      {
        global: {
          plugins: [router],
        },
      }
    )

    expect(wrapper.html()).toContain('div')
    expect(router.currentRoute.value.path).toBe('/')
  })

  it('переходит на страницу about', async () => {
    router.push('/about')
    await router.isReady()

    const wrapper = mount(
      {
        template: '<router-view></router-view>',
      },
      {
        global: {
          plugins: [router],
        },
      }
    )

    expect(wrapper.html()).toContain('div')
    expect(router.currentRoute.value.path).toBe('/about')
  })
})
