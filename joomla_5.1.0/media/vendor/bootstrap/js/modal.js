import { E as EventHandler, S as SelectorEngine, i as isVisible, e as enableDismissTrigger, d as defineJQueryPlugin, B as BaseComponent, k as ScrollBarHelper, l as Backdrop, F as FocusTrap, r as reflow, b as isRTL } from './dom.js?5.3.2';

/**
 * --------------------------------------------------------------------------
 * Bootstrap modal.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/main/LICENSE)
 * --------------------------------------------------------------------------
 */

/**
 * Constants
 */

const NAME = 'modal';
const DATA_KEY = 'bs.modal';
const EVENT_KEY = `.${DATA_KEY}`;
const DATA_API_KEY = '.data-api';
const ESCAPE_KEY = 'Escape';
const EVENT_HIDE = `hide${EVENT_KEY}`;
const EVENT_HIDE_PREVENTED = `hidePrevented${EVENT_KEY}`;
const EVENT_HIDDEN = `hidden${EVENT_KEY}`;
const EVENT_SHOW = `show${EVENT_KEY}`;
const EVENT_SHOWN = `shown${EVENT_KEY}`;
const EVENT_RESIZE = `resize${EVENT_KEY}`;
const EVENT_CLICK_DISMISS = `click.dismiss${EVENT_KEY}`;
const EVENT_MOUSEDOWN_DISMISS = `mousedown.dismiss${EVENT_KEY}`;
const EVENT_KEYDOWN_DISMISS = `keydown.dismiss${EVENT_KEY}`;
const EVENT_CLICK_DATA_API = `click${EVENT_KEY}${DATA_API_KEY}`;
const CLASS_NAME_OPEN = 'modal-open';
const CLASS_NAME_FADE = 'fade';
const CLASS_NAME_SHOW = 'show';
const CLASS_NAME_STATIC = 'modal-static';
const OPEN_SELECTOR = '.modal.show';
const SELECTOR_DIALOG = '.modal-dialog';
const SELECTOR_MODAL_BODY = '.modal-body';
const SELECTOR_DATA_TOGGLE = '[data-bs-toggle="modal"]';
const Default = {
  backdrop: true,
  focus: true,
  keyboard: true
};
const DefaultType = {
  backdrop: '(boolean|string)',
  focus: 'boolean',
  keyboard: 'boolean'
};

/**
 * Class definition
 */

class Modal extends BaseComponent {
  constructor(element, config) {
    super(element, config);
    this._dialog = SelectorEngine.findOne(SELECTOR_DIALOG, this._element);
    this._backdrop = this._initializeBackDrop();
    this._focustrap = this._initializeFocusTrap();
    this._isShown = false;
    this._isTransitioning = false;
    this._scrollBar = new ScrollBarHelper();
    this._addEventListeners();
  }

  // Getters
  static get Default() {
    return Default;
  }
  static get DefaultType() {
    return DefaultType;
  }
  static get NAME() {
    return NAME;
  }

  // Public
  toggle(relatedTarget) {
    return this._isShown ? this.hide() : this.show(relatedTarget);
  }
  show(relatedTarget) {
    if (this._isShown || this._isTransitioning) {
      return;
    }
    const showEvent = EventHandler.trigger(this._element, EVENT_SHOW, {
      relatedTarget
    });
    if (showEvent.defaultPrevented) {
      return;
    }
    this._isShown = true;
    this._isTransitioning = true;
    this._scrollBar.hide();
    document.body.classList.add(CLASS_NAME_OPEN);
    this._adjustDialog();
    this._backdrop.show(() => this._showElement(relatedTarget));
  }
  hide() {
    if (!this._isShown || this._isTransitioning) {
      return;
    }
    const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE);
    if (hideEvent.defaultPrevented) {
      return;
    }
    this._isShown = false;
    this._isTransitioning = true;
    this._focustrap.deactivate();
    this._element.classList.remove(CLASS_NAME_SHOW);
    this._queueCallback(() => this._hideModal(), this._element, this._isAnimated());
  }
  dispose() {
    EventHandler.off(window, EVENT_KEY);
    EventHandler.off(this._dialog, EVENT_KEY);
    this._backdrop.dispose();
    this._focustrap.deactivate();
    super.dispose();
  }
  handleUpdate() {
    this._adjustDialog();
  }

  // Private
  _initializeBackDrop() {
    return new Backdrop({
      isVisible: Boolean(this._config.backdrop),
      // 'static' option will be translated to true, and booleans will keep their value,
      isAnimated: this._isAnimated()
    });
  }
  _initializeFocusTrap() {
    return new FocusTrap({
      trapElement: this._element
    });
  }
  _showElement(relatedTarget) {
    // try to append dynamic modal
    if (!document.body.contains(this._element)) {
      document.body.append(this._element);
    }
    this._element.style.display = 'block';
    this._element.removeAttribute('aria-hidden');
    this._element.setAttribute('aria-modal', true);
    this._element.setAttribute('role', 'dialog');
    this._element.scrollTop = 0;
    const modalBody = SelectorEngine.findOne(SELECTOR_MODAL_BODY, this._dialog);
    if (modalBody) {
      modalBody.scrollTop = 0;
    }
    reflow(this._element);
    this._element.classList.add(CLASS_NAME_SHOW);
    const transitionComplete = () => {
      if (this._config.focus) {
        this._focustrap.activate();
      }
      this._isTransitioning = false;
      EventHandler.trigger(this._element, EVENT_SHOWN, {
        relatedTarget
      });
    };
    this._queueCallback(transitionComplete, this._dialog, this._isAnimated());
  }
  _addEventListeners() {
    EventHandler.on(this._element, EVENT_KEYDOWN_DISMISS, event => {
      if (event.key !== ESCAPE_KEY) {
        return;
      }
      if (this._config.keyboard) {
        this.hide();
        return;
      }
      this._triggerBackdropTransition();
    });
    EventHandler.on(window, EVENT_RESIZE, () => {
      if (this._isShown && !this._isTransitioning) {
        this._adjustDialog();
      }
    });
    EventHandler.on(this._element, EVENT_MOUSEDOWN_DISMISS, event => {
      // a bad trick to segregate clicks that may start inside dialog but end outside, and avoid listen to scrollbar clicks
      EventHandler.one(this._element, EVENT_CLICK_DISMISS, event2 => {
        if (this._element !== event.target || this._element !== event2.target) {
          return;
        }
        if (this._config.backdrop === 'static') {
          this._triggerBackdropTransition();
          return;
        }
        if (this._config.backdrop) {
          this.hide();
        }
      });
    });
  }
  _hideModal() {
    this._element.style.display = 'none';
    this._element.setAttribute('aria-hidden', true);
    this._element.removeAttribute('aria-modal');
    this._element.removeAttribute('role');
    this._isTransitioning = false;
    this._backdrop.hide(() => {
      document.body.classList.remove(CLASS_NAME_OPEN);
      this._resetAdjustments();
      this._scrollBar.reset();
      EventHandler.trigger(this._element, EVENT_HIDDEN);
    });
  }
  _isAnimated() {
    return this._element.classList.contains(CLASS_NAME_FADE);
  }
  _triggerBackdropTransition() {
    const hideEvent = EventHandler.trigger(this._element, EVENT_HIDE_PREVENTED);
    if (hideEvent.defaultPrevented) {
      return;
    }
    const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
    const initialOverflowY = this._element.style.overflowY;
    // return if the following background transition hasn't yet completed
    if (initialOverflowY === 'hidden' || this._element.classList.contains(CLASS_NAME_STATIC)) {
      return;
    }
    if (!isModalOverflowing) {
      this._element.style.overflowY = 'hidden';
    }
    this._element.classList.add(CLASS_NAME_STATIC);
    this._queueCallback(() => {
      this._element.classList.remove(CLASS_NAME_STATIC);
      this._queueCallback(() => {
        this._element.style.overflowY = initialOverflowY;
      }, this._dialog);
    }, this._dialog);
    this._element.focus();
  }

  /**
   * The following methods are used to handle overflowing modals
   */

  _adjustDialog() {
    const isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;
    const scrollbarWidth = this._scrollBar.getWidth();
    const isBodyOverflowing = scrollbarWidth > 0;
    if (isBodyOverflowing && !isModalOverflowing) {
      const property = isRTL() ? 'paddingLeft' : 'paddingRight';
      this._element.style[property] = `${scrollbarWidth}px`;
    }
    if (!isBodyOverflowing && isModalOverflowing) {
      const property = isRTL() ? 'paddingRight' : 'paddingLeft';
      this._element.style[property] = `${scrollbarWidth}px`;
    }
  }
  _resetAdjustments() {
    this._element.style.paddingLeft = '';
    this._element.style.paddingRight = '';
  }

  // Static
  static jQueryInterface(config, relatedTarget) {
    return this.each(function () {
      const data = Modal.getOrCreateInstance(this, config);
      if (typeof config !== 'string') {
        return;
      }
      if (typeof data[config] === 'undefined') {
        throw new TypeError(`No method named "${config}"`);
      }
      data[config](relatedTarget);
    });
  }
}

/**
 * Data API implementation
 */

EventHandler.on(document, EVENT_CLICK_DATA_API, SELECTOR_DATA_TOGGLE, function (event) {
  const target = SelectorEngine.getElementFromSelector(this);
  if (['A', 'AREA'].includes(this.tagName)) {
    event.preventDefault();
  }
  EventHandler.one(target, EVENT_SHOW, showEvent => {
    if (showEvent.defaultPrevented) {
      // only register focus restorer if modal will actually get shown
      return;
    }
    EventHandler.one(target, EVENT_HIDDEN, () => {
      if (isVisible(this)) {
        this.focus();
      }
    });
  });

  // avoid conflict when clicking modal toggler while another one is open
  const alreadyOpen = SelectorEngine.findOne(OPEN_SELECTOR);
  if (alreadyOpen) {
    Modal.getInstance(alreadyOpen).hide();
  }
  const data = Modal.getOrCreateInstance(target);
  data.toggle(this);
});
enableDismissTrigger(Modal);

/**
 * jQuery
 */

defineJQueryPlugin(Modal);

Joomla = Joomla || {};
Joomla.Modal = Joomla.Modal || {};
window.bootstrap = window.bootstrap || {};
window.bootstrap.Modal = Modal;
const allowed = {
  iframe: ['src', 'name', 'width', 'height']
};
Joomla.initialiseModal = (modal, options) => {
  if (!(modal instanceof Element)) {
    return;
  }

  // eslint-disable-next-line no-new
  new window.bootstrap.Modal(modal, options);

  // Comply with the Joomla API - Bound element.open/close
  modal.open = () => {
    window.bootstrap.Modal.getInstance(modal).show(modal);
  };
  modal.close = () => {
    window.bootstrap.Modal.getInstance(modal).hide();
  };

  // Do some Joomla specific changes
  modal.addEventListener('show.bs.modal', () => {
    // Comply with the Joomla API - Set the current Modal ID
    Joomla.Modal.setCurrent(modal);
    if (modal.dataset.url) {
      const modalBody = modal.querySelector('.modal-body');
      const iframe = modalBody.querySelector('iframe');
      if (iframe) {
        const addData = modal.querySelector('joomla-field-mediamore');
        if (addData) {
          addData.parentNode.removeChild(addData);
        }
        iframe.parentNode.removeChild(iframe);
      }

      // @todo merge https://github.com/joomla/joomla-cms/pull/20788
      // Hacks because com_associations and field modals use pure javascript in the url!
      if (modal.dataset.iframe.indexOf('document.getElementById') > 0) {
        const iframeTextArr = modal.dataset.iframe.split('+');
        const idFieldArr = iframeTextArr[1].split('"');
        let el;
        idFieldArr[0] = idFieldArr[0].replace(/&quot;/g, '"');
        if (!document.getElementById(idFieldArr[1])) {
          // eslint-disable-next-line no-new-func
          const fn = new Function(`return ${idFieldArr[0]}`); // This is UNSAFE!!!!
          el = fn.call(null);
        } else {
          el = document.getElementById(idFieldArr[1]).value;
        }
        modalBody.insertAdjacentHTML('afterbegin', Joomla.sanitizeHtml(`${iframeTextArr[0]}${el}${iframeTextArr[2]}`, allowed));
      } else {
        modalBody.insertAdjacentHTML('afterbegin', Joomla.sanitizeHtml(modal.dataset.iframe, allowed));
      }
    }
  });
  modal.addEventListener('shown.bs.modal', () => {
    const modalBody = modal.querySelector('.modal-body');
    const modalHeader = modal.querySelector('.modal-header');
    const modalFooter = modal.querySelector('.modal-footer');
    let modalHeaderHeight = 0;
    let modalFooterHeight = 0;
    let maxModalBodyHeight = 0;
    let modalBodyPadding = 0;
    let modalBodyHeightOuter = 0;
    if (modalBody) {
      if (modalHeader) {
        const modalHeaderRects = modalHeader.getBoundingClientRect();
        modalHeaderHeight = modalHeaderRects.height;
        modalBodyHeightOuter = modalBody.offsetHeight;
      }
      if (modalFooter) {
        modalFooterHeight = parseFloat(getComputedStyle(modalFooter, null).height.replace('px', ''));
      }
      const modalBodyHeight = parseFloat(getComputedStyle(modalBody, null).height.replace('px', ''));
      const padding = modalBody.offsetTop;
      const maxModalHeight = parseFloat(getComputedStyle(document.body, null).height.replace('px', '')) - padding * 2;
      modalBodyPadding = modalBodyHeightOuter - modalBodyHeight;
      maxModalBodyHeight = maxModalHeight - (modalHeaderHeight + modalFooterHeight + modalBodyPadding);
    }
    if (modal.dataset.url) {
      const iframeEl = modal.querySelector('iframe');
      const iframeHeight = parseFloat(getComputedStyle(iframeEl, null).height.replace('px', ''));
      if (iframeHeight > maxModalBodyHeight) {
        modalBody.style.maxHeight = maxModalBodyHeight;
        modalBody.style.overflowY = 'auto';
        iframeEl.style.maxHeight = maxModalBodyHeight - modalBodyPadding;
      }
    }
  });
  modal.addEventListener('hide.bs.modal', () => {
    const modalBody = modal.querySelector('.modal-body');
    modalBody.style.maxHeight = 'initial';
  });
  modal.addEventListener('hidden.bs.modal', () => {
    // Comply with the Joomla API - Remove the current Modal ID
    Joomla.Modal.setCurrent('');
  });
};

/**
 * Method to invoke a click on button inside an iframe
 *
 * @param   {object}  options  Object with the css selector for the parent element of an iframe
 *                             and the selector of the button in the iframe that will be clicked
 *                             { iframeSelector: '', buttonSelector: '' }
 * @returns {boolean}
 *
 * @since   4.0.0
 */
Joomla.iframeButtonClick = options => {
  if (!options.iframeSelector || !options.buttonSelector) {
    throw new Error('Selector is missing');
  }
  const iframe = document.querySelector(`${options.iframeSelector} iframe`);
  if (iframe) {
    const button = iframe.contentWindow.document.querySelector(options.buttonSelector);
    if (button) {
      button.click();
    }
  }
};
if (Joomla && Joomla.getOptions) {
  // Get the elements/configurations from the PHP
  const modals = Joomla.getOptions('bootstrap.modal');
  // Initialise the elements
  if (typeof modals === 'object' && modals !== null) {
    Object.keys(modals).forEach(modal => {
      const opt = modals[modal];
      const options = {
        backdrop: opt.backdrop ? opt.backdrop : true,
        keyboard: opt.keyboard ? opt.keyboard : true,
        focus: opt.focus ? opt.focus : true
      };
      Array.from(document.querySelectorAll(modal)).map(modalEl => Joomla.initialiseModal(modalEl, options));
    });
  }
}

export { Modal as M };
