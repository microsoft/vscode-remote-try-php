import { E as EventHandler, d as defineJQueryPlugin, B as BaseComponent } from './dom.js?5.3.2';

/**
 * --------------------------------------------------------------------------
 * Bootstrap button.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
 * --------------------------------------------------------------------------
 */

/**
 * Constants
 */

const NAME = 'button';
const DATA_KEY = 'bs.button';
const EVENT_KEY = `.${DATA_KEY}`;
const DATA_API_KEY = '.data-api';
const CLASS_NAME_ACTIVE = 'active';
const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="button"]';
const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;

/**
 * Class definition
 */

class Button extends BaseComponent {
  // Getters
  static get NAME() {
    return NAME;
  }

  // Public
  toggle() {
    // Toggle class and sync the `aria-pressed` attribute with the return value of the `.toggle()` method
    this._element.setAttribute('aria-pressed', this._element.classList.toggle(CLASS_NAME_ACTIVE));
  }

  // Static
  static jQueryInterface(config) {
    return this.each(function () {
      const data = Button.getOrCreateInstance(this);
      if (config === 'toggle') {
        data[config]();
      }
    });
  }
}

/**
 * Data API implementation
 */

EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, event => {
  event.preventDefault();
  const button = event.target.closest(SELECTOR_DATA_TOGGLE);
  const data = Button.getOrCreateInstance(button);
  data.toggle();
});

/**
 * jQuery
 */

defineJQueryPlugin(Button);

window.bootstrap = window.bootstrap || {};
window.bootstrap.Button = Button;
if (Joomla && Joomla.getOptions) {
  // Get the elements/configurations from the PHP
  const buttons = Joomla.getOptions('bootstrap.button');
  // Initialise the elements
  if (buttons && buttons.length) {
    buttons.forEach(selector => {
      Array.from(document.querySelectorAll(selector)).map(el => new window.bootstrap.Button(el));
    });
  }
}

export { Button as B };
