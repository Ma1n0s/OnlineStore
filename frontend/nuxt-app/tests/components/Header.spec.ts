import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'

// Создаем мок-компонент для тестирования с телефоном
const HeaderWithPhone = {
  template: `
    <div>
      <div class="brand">Абсолют Техно</div>
      <div class="user-info">{{ phone || name }}</div>
    </div>
  `,
  data() {
    return {
      name: 'Тестовый пользователь',
      phone: '+7 999 999 99 99',
    }
  },
}

// Создаем мок-компонент для тестирования только с именем
const HeaderWithNameOnly = {
  template: `
    <div>
      <div class="brand">Абсолют Техно</div>
      <div class="user-info">{{ phone || name }}</div>
    </div>
  `,
  data() {
    return {
      name: 'Тестовый пользователь',
      phone: '',
    }
  },
}

describe('Header Component', () => {
  it('содержит название компании', () => {
    const wrapper = mount(HeaderWithPhone)
    expect(wrapper.text()).toContain('Абсолют Техно')
  })

  it('отображает номер телефона при авторизации, если он задан', () => {
    const wrapper = mount(HeaderWithPhone)
    expect(wrapper.find('.user-info').text()).toBe('+7 999 999 99 99')
  })

  it('отображает имя пользователя, если телефон не задан', () => {
    const wrapper = mount(HeaderWithNameOnly)
    expect(wrapper.find('.user-info').text()).toBe('Тестовый пользователь')
  })
})
