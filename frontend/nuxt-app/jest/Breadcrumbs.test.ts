import { getBreadcrumbs, getBreadcrumbsFromCategoryPath } from '~/components/BreadCrumbs/helpers'

describe('Проверка Хлебных крошек', () => {
  test('Проверка Хлебных крошек из масива значений', () => {
    const val = ['Инструменты', 'Шуруповерты']

    expect(getBreadcrumbs(val)).toStrictEqual([
      { name: 'Инструменты', url: '/category/Инструменты' },
      { name: 'Шуруповерты', url: '/category/Шуруповерты' },
    ])
  })

  test('Проверка Хлебных крошек из пути в категории', () => {
    const val = [
      { name: 'Инструменты', slug: 'instrumenty' },
      { name: 'Шуруповерты', slug: 'shurupoverty' },
    ]

    expect(getBreadcrumbsFromCategoryPath(val)).toEqual([
      { name: 'Инструменты', url: '/category/instrumenty' },
      { name: 'Шуруповерты', url: '/category/shurupoverty' },
    ])
  })
})
