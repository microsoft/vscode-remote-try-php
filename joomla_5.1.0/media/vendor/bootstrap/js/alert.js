import { e as enableDismissTrigger, d as defineJQueryPlugin, B as BaseComponent, E as EventHandler } from './dom.js?5.3.2';

/**
 * --------------------------------------------------------------------------
 * Bootstrap alert.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
 * --------------------------------------------------------------------------
 */

/**
 * Constants
 */

const NAME = 'alert';
const DATA_KEY = 'bs.alert';
const EVENT_KEY = `.${DATA_KEY}`;
const EVENT_CLOSE = `close${EVENT_KEY}`;
const EVENT_CLOSED = `closed${EVENT_KEY}`;
const CLASS_NAME_FADE = 'fade';
const CLASS_NAME_SHOW = 'show';

/**
 * Class definition
 */

class Alert extends BaseComponent {
  // Getters
  static get NAME() {
    return NAME;
  }

  // Public
  close() {
    const closeEvent = EventHandler.trigger(this._element, EVENT_CLOSE);
    if (closeEvent.defaultPrevented) {
      return;
    }
    this._element.classList.remove(CLASS_NAME_SHOW);
    const isAnimated = this._element.classList.contains(CLASS_NAME_FADE);
    this._queueCallback(() => this._destroyElement(), this._element, isAnimated);
  }

  // Private
  _destroyElement() {
    this._element.remove();
    EventHandler.trigger(this._element, EVENT_CLOSED);
    this.dispose();
  }

  // Static
  static jQueryInterface(config) {
    return this.each(function () {
      const data = Alert.getOrCreateInstance(this);
      if (typeof config !== 'string') {
        return;
      }
      if (data[config] === undefined || config.startsWith('_') || config === 'constructor') {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config](this);
    });
  }
}

/**
 * Data API implementation
 */

enableDismissTrigger(Alert, 'close');

/**
 * jQuery
 */

defineJQueryPlugin(Alert);

window.bootstrap = window.bootstrap || {};
window.bootstrap.Alert = Alert;
if (Joomla && Joomla.getOptions) {
  // Get the elements/configurations from the PHP
  const alerts = Joomla.getOptions('bootstrap.alert');
  // Initialise the elements
  if (alerts && alerts.length) {
    alerts.forEach(selector => {
      Array.from(document.querySelectorAll(selector)).map(el => new window.bootstrap.Alert(el));
    });
  }
}

export { Alert as A };
