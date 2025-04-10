/// <reference types="cypress" />

// ***********************************************
// Этот файл содержит пользовательские команды для Cypress
// ***********************************************

// Объявление типов для пользовательских команд
declare global {
  // eslint-disable-next-line no-unused-vars
  namespace Cypress {
    interface Chainable {
      /**
       * Стабилизирует страницу для снятия скриншота, скрывая динамический контент
       * @example cy.stabilizeForScreenshot()
       */
      stabilizeForScreenshot(): Chainable<Element>
    }
  }
}

// Импортируем команды из файла e2e.ts
import './e2e'
