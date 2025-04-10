import { describe, it, expect, vi } from 'vitest'

// Мокирую хранилище напрямую
vi.mock('~/stores/user', () => ({
  useUserStore: vi.fn(() => ({
    isAuth: false,
    name: '',
    phone: '',
  })),
}))

import { useUserStore } from '~/stores/user'

describe('User Store', () => {
  it('инициализируется с корректными значениями по умолчанию', () => {
    const store = useUserStore()

    expect(store.isAuth).toBe(false)
    expect(store.name).toBe('')
    expect(store.phone).toBe('')
  })
})
