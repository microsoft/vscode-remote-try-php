class AlertElement extends HTMLElement {
  constructor() {
    super();

    // Bindings
    this.close = this.close.bind(this);
    this.destroyCloseButton = this.destroyCloseButton.bind(this);
    this.createCloseButton = this.createCloseButton.bind(this);
    this.onMutation = this.onMutation.bind(this);

    this.observer = new MutationObserver(this.onMutation);
    this.observer.observe(this, { attributes: false, childList: true, subtree: true });

    // Handle the fade in animation
    this.addEventListener('animationend', (event) => {
      if (event.animationName === 'joomla-alert-fade-in' && event.target === this) {
        this.dispatchEvent(new CustomEvent('joomla.alert.shown'));
        this.style.removeProperty('animationName');
      }
    });

    // Handle the fade out animation
    this.addEventListener('animationend', (event) => {
      if (event.animationName === 'joomla-alert-fade-out' && event.target === this) {
        this.dispatchEvent(new CustomEvent('joomla.alert.closed'));
        this.remove();
      }
    });
  }

  /* Attributes to monitor */
  static get observedAttributes() { return ['type', 'role', 'dismiss', 'auto-dismiss', 'close-text']; }

  get type() { return this.getAttribute('type'); }

  set type(value) { this.setAttribute('type', value); }

  get role() { return this.getAttribute('role'); }

  set role(value) { this.setAttribute('role', value); }

  get closeText() { return this.getAttribute('close-text'); }

  set closeText(value) { this.setAttribute('close-text', value); }

  get dismiss() { return this.getAttribute('dismiss'); }

  set dismiss(value) { this.setAttribute('dismiss', value); }

  get autodismiss() { return this.getAttribute('auto-dismiss'); }

  set autodismiss(value) { this.setAttribute('auto-dismiss', value); }

  /* Lifecycle, element appended to the DOM */
  connectedCallback() {
    this.dispatchEvent(new CustomEvent('joomla.alert.show'));
    this.style.animationName = 'joomla-alert-fade-in';

    // Default to info
    if (!this.type || !['info', 'warning', 'danger', 'success'].includes(this.type)) {
      this.setAttribute('type', 'info');
    }
    // Default to alert
    if (!this.role || !['alert', 'alertdialog'].includes(this.role)) {
      this.setAttribute('role', 'alert');
    }

    // Hydrate the button
    if (this.firstElementChild && this.firstElementChild.tagName === 'BUTTON') {
      this.button = this.firstElementChild;
      if (this.button.classList.contains('joomla-alert--close')) {
        this.button.classList.add('joomla-alert--close');
      }
      if (this.button.innerHTML === '') {
        this.button.innerHTML = '<span aria-hidden="true">&times;</span>';
      }
      if (!this.button.hasAttribute('aria-label')) {
        this.button.setAttribute('aria-label', this.closeText);
      }
    }

    // Append button
    if (this.hasAttribute('dismiss') && !this.button) {
      this.createCloseButton();
    }

    if (this.hasAttribute('auto-dismiss')) {
      this.autoDismiss();
    }
  }

  /* Lifecycle, element removed from the DOM */
  disconnectedCallback() {
    if (this.button) {
      this.button.removeEventListener('click', this.close);
    }
    this.observer.disconnect();
  }

  /* Respond to attribute changes */
  attributeChangedCallback(attr, oldValue, newValue) {
    switch (attr) {
      case 'type':
        if (!newValue || (newValue && ['info', 'warning', 'danger', 'success'].indexOf(newValue) === -1)) {
          this.type = 'info';
        }
        break;
      case 'role':
        if (!newValue || (newValue && ['alert', 'alertdialog'].indexOf(newValue) === -1)) {
          this.role = 'alert';
        }
        break;
      case 'dismiss':
        if ((!newValue || newValue === '') && (!oldValue || oldValue === '')) {
          if (this.button && !this.hasAttribute('dismiss')) {
            this.destroyCloseButton();
          } else if (!this.button && this.hasAttribute('dismiss')) {
            this.createCloseButton();
          }
        } else if (this.button && newValue === 'false') {
          this.destroyCloseButton();
        } else if (!this.button && newValue !== 'false') {
          this.createCloseButton();
        }
        break;
      case 'close-text':
        if (!newValue || newValue !== oldValue) {
          if (this.button) {
            this.button.setAttribute('aria-label', newValue);
          }
        }
        break;
      case 'auto-dismiss':
        this.autoDismiss();
        break;
    }
  }

  /* Observe added elements */
  onMutation(mutationsList) {
    // eslint-disable-next-line no-restricted-syntax
    for (const mutation of mutationsList) {
      if (mutation.type === 'childList') {
        if (mutation.addedNodes.length) {
          // Make sure that the button is always the first element
          if (this.button && this.firstElementChild !== this.button) {
            this.prepend(this.button);
          }
        }
      }
    }
  }

  /* Method to close the alert */
  close() {
    this.dispatchEvent(new CustomEvent('joomla.alert.close'));
    this.style.animationName = 'joomla-alert-fade-out';
  }

  /* Method to create the close button */
  createCloseButton() {
    this.button = document.createElement('button');
    this.button.setAttribute('type', 'button');
    this.button.classList.add('joomla-alert--close');
    this.button.innerHTML = '<span aria-hidden="true">&times;</span>';
    this.button.setAttribute('aria-label', this.closeText);
    this.insertAdjacentElement('afterbegin', this.button);

    /* Add the required listener */
    this.button.addEventListener('click', this.close);
  }

  /* Method to remove the close button */
  destroyCloseButton() {
    if (this.button) {
      this.button.removeEventListener('click', this.close);
      this.button.parentNode.removeChild(this.button);
      this.button = null;
    }
  }

  /* Method to auto-dismiss */
  autoDismiss() {
    const timer = parseInt(this.getAttribute('auto-dismiss'), 10);
    setTimeout(this.close, timer >= 10 ? timer : 3000);
  }
}

if (!customElements.get('joomla-alert')) {
  customElements.define('joomla-alert', AlertElement);
}
