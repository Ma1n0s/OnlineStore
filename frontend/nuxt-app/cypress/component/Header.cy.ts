import { mount } from 'cypress/vue'
import Header from '../../components/Header/Header.vue'

describe('Header компонент', () => {
  it('отображается корректно', () => {
    // Монтируем компонент Header
    mount(Header, {
      global: {
        stubs: {
          NuxtImg: true,
          NuxtLink: true,
          Icon: true,
        },
      },
    })

    // Проверяем, что заголовок сайта отображается
    cy.contains('Абсолют Техно').should('be.visible')
  })

  it('содержит ссылку на главную страницу', () => {
    mount(Header, {
      global: {
        stubs: {
          NuxtImg: true,
          NuxtLink: true,
          Icon: true,
        },
      },
    })

    // Ищем ссылку на главную (стабированный NuxtLink)
    cy.get('NuxtLink[to="/"]').should('exist')
  })
})
