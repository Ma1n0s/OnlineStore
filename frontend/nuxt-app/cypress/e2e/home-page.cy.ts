describe('Домашняя страница', () => {
  beforeEach(() => {
    // Посещаем домашнюю страницу перед каждым тестом
    cy.visit('/')
  })

  it('загружается и отображает ключевые элементы', () => {
    // Проверяем наличие заголовка и логотипа
    cy.contains('Абсолют Техно').should('be.visible')
    cy.get('img').should('be.visible')

    // Делаем скриншот домашней страницы
    cy.stabilizeForScreenshot()
    cy.percySnapshot('Домашняя страница')
  })

  it('отображает слайдеры на домашней странице', () => {
    // Проверяем наличие слайдеров
    cy.get('.swiper-container').should('have.length.at.least', 1)

    // Делаем скриншот слайдеров
    cy.stabilizeForScreenshot()
    cy.percySnapshot('Слайдеры на домашней странице')
  })

  it('имеет рабочие ссылки в шапке сайта', () => {
    // Проверяем, что ссылка на главную работает
    cy.get('a[href="/"]').should('be.visible')

    // Проверяем, что кнопка каталога отображается
    cy.contains('Каталог').should('be.visible')

    // Проверяем наличие поиска
    cy.get('input[type="search"]').should('exist')
  })
})
