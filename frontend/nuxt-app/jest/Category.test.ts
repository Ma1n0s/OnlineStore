import { getPath } from '~/components/CategoryItems/CategoryList/CategoryList'

describe('Получение пути для списка категории', () => {
  const pathPrefix = '/Prefix/'

  test('Тест getPath где есть продукты', () => {
    const category = {
      haveProducts: true,
      slug: 'Тестовый продукт',
    }
    expect(getPath(category, pathPrefix)).toMatch('/products/Prefix/Тестовый продукт')
  })

  test('Тест getPath где нет продуктов', () => {
    const category = {
      haveProducts: false,
      slug: 'Тестовый продукт',
    }
    expect(getPath(category, pathPrefix)).toMatch('/Prefix/Тестовый продукт')
  })
})
