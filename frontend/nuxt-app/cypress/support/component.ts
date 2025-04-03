// ***********************************************************
// Этот файл используется для настройки компонентного тестирования в Cypress
// ***********************************************************

// Импортируем необходимые зависимости
import { mount } from "cypress/vue";

// Делаем mount доступным как команду Cypress
Cypress.Commands.add("mount", mount);

// Объявляем типы для команды mount
declare global {
  namespace Cypress {
    interface Chainable {
      /**
       * Монтирует Vue компонент
       * @example cy.mount(MyComponent)
       */
      mount: typeof mount;
    }
  }
}
