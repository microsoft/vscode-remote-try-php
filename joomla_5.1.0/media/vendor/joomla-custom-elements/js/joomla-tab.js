class TabElement extends HTMLElement {}

customElements.define('joomla-tab-element', TabElement);

class TabsElement extends HTMLElement {
  /* Attributes to monitor */
  static get observedAttributes() { return ['recall', 'orientation', 'view', 'breakpoint']; }

  get recall() { return this.getAttribute('recall'); }

  set recall(value) { this.setAttribute('recall', value); }

  get view() { return this.getAttribute('view'); }

  set view(value) { this.setAttribute('view', value); }

  get orientation() { return this.getAttribute('orientation'); }

  set orientation(value) { this.setAttribute('orientation', value); }

  get breakpoint() { return parseInt(this.getAttribute('breakpoint'), 10); }

  set breakpoint(value) { this.setAttribute('breakpoint', value); }

  /* Lifecycle, element created */
  constructor() {
    super();
    this.tabs = [];
    this.tabsElements = [];
    this.previousActive = null;

    this.onMutation = this.onMutation.bind(this);
    this.keyBehaviour = this.keyBehaviour.bind(this);
    this.activateTab = this.activateTab.bind(this);
    this.deactivateTabs = this.deactivateTabs.bind(this);
    this.checkView = this.checkView.bind(this);

    this.observer = new MutationObserver(this.onMutation);
    this.observer.observe(this, { attributes: false, childList: true, subtree: true });
  }

  /* Lifecycle, element appended to the DOM */
  connectedCallback() {
    if (!this.orientation || (this.orientation && !['horizontal', 'vertical'].includes(this.orientation))) {
      this.orientation = 'horizontal';
    }

    if (!this.view || (this.view && !['tabs', 'accordion'].includes(this.view))) {
      this.view = 'tabs';
    }

    // get tab elements
    this.tabsElements = [].slice.call(this.children).filter((el) => el.tagName.toLowerCase() === 'joomla-tab-element');

    // Sanity checks
    if (!this.tabsElements.length) {
      return;
    }

    this.isNested = this.parentNode.closest('joomla-tab') instanceof HTMLElement;

    this.hydrate();
    if (this.hasAttribute('recall') && !this.isNested) {
      this.activateFromState();
    }

    // Activate tab from the URL hash
    if (window.location.hash) {
      const hash = window.location.hash.substr(1);
      const tabToactivate = this.tabs.filter((tab) => tab.tab.id === hash);
      if (tabToactivate.length) {
        this.activateTab(tabToactivate[0].tab, false);
      }
    }

    // If no active tab activate the first one
    if (!this.tabs.filter((tab) => tab.tab.hasAttribute('active')).length) {
      this.activateTab(this.tabs[0].tab, false);
    }

    this.addEventListener('keyup', this.keyBehaviour);

    if (this.breakpoint) {
      // Convert tabs to accordian
      this.checkView();
      window.addEventListener('resize', () => {
        this.checkView();
      });
    }
  }

  /* Lifecycle, element removed from the DOM */
  disconnectedCallback() {
    this.tabs.map((tab) => {
      tab.tabButton.removeEventListener('click', this.activateTab);
      tab.accordionButton.removeEventListener('click', this.activateTab);
      return tab;
    });
    this.removeEventListener('keyup', this.keyBehaviour);
  }

  /* Respond to attribute changes */
  attributeChangedCallback(attr, oldValue, newValue) {
    switch (attr) {
      case 'view':
        if (!newValue || (newValue && !['tabs', 'accordion'].includes(newValue))) {
          this.view = 'tabs';
        }
        if (newValue === 'tabs' && newValue !== oldValue) {
          if (this.tabButtonContainer) this.tabButtonContainer.removeAttribute('hidden');
          this.tabs.map((tab) => tab.accordionButton.setAttribute('hidden', ''));
        } else if (newValue === 'accordion' && newValue !== oldValue) {
          if (this.tabButtonContainer) this.tabButtonContainer.setAttribute('hidden', '');
          this.tabs.map((tab) => tab.accordionButton.removeAttribute('hidden'));
        }
        break;
    }
  }

  hydrate() {
    // Ensure the tab links container exists
    this.tabButtonContainer = document.createElement('div');
    this.tabButtonContainer.setAttribute('role', 'tablist');
    this.insertAdjacentElement('afterbegin', this.tabButtonContainer);

    if (this.view === 'accordion') {
      this.tabButtonContainer.setAttribute('hidden', '');
    }

    this.tabsElements.map((tab) => {
      // Create Accordion button
      const accordionButton = document.createElement('button');
      accordionButton.setAttribute('aria-expanded', !!tab.hasAttribute('active'));
      accordionButton.setAttribute('aria-controls', tab.id);
      accordionButton.setAttribute('type', 'button');
      accordionButton.innerHTML = `<span class="accordion-title">${tab.getAttribute('name')}<span class="accordion-icon"></span></span>`;
      tab.insertAdjacentElement('beforebegin', accordionButton);

      if (this.view === 'tabs') {
        accordionButton.setAttribute('hidden', '');
      }

      accordionButton.addEventListener('click', this.activateTab);

      // Create tab button
      const tabButton = document.createElement('button');
      tabButton.setAttribute('aria-expanded', !!tab.hasAttribute('active'));
      tabButton.setAttribute('aria-controls', tab.id);
      tabButton.setAttribute('role', 'tab');
      tabButton.setAttribute('type', 'button');
      tabButton.innerHTML = `${tab.getAttribute('name')}`;
      this.tabButtonContainer.appendChild(tabButton);

      tabButton.addEventListener('click', this.activateTab);

      if (this.view === 'tabs') {
        tab.setAttribute('role', 'tabpanel');
      } else {
        tab.setAttribute('role', 'region');
      }

      this.tabs.push({
        tab,
        tabButton,
        accordionButton,
      });

      return tab;
    });
  }

  /* Update on mutation */
  onMutation(mutationsList) {
    // eslint-disable-next-line no-restricted-syntax
    for (const mutation of mutationsList) {
      if (mutation.type === 'childList') {
        if (mutation.addedNodes.length) {
          [].slice.call(mutation.addedNodes).map((inserted) => this.createNavs(inserted));
          // Add the tab buttons
        }
        if (mutation.removedNodes.length) {
          // Remove the tab buttons
          [].slice.call(mutation.addedNodes).map((inserted) => this.removeNavs(inserted));
        }
      }
    }
  }

  keyBehaviour(e) {
    // Only the tabs/accordion buttons, no ⌘ or Alt modifier
    if (![...this.tabs.map((el) => el.tabButton), ...this.tabs.map((el) => el.accordionButton)]
      .includes(document.activeElement)
      || e.metaKey
      || e.altKey) {
      return;
    }

    let previousTabItem;
    let nextTabItem;
    if (this.view === 'tabs') {
      const currentTabIndex = this.tabs.findIndex((tab) => tab.tab.hasAttribute('active'));
      previousTabItem = currentTabIndex - 1 >= 0
        ? this.tabs[currentTabIndex - 1] : this.tabs[this.tabs.length - 1];
      nextTabItem = currentTabIndex + 1 <= this.tabs.length - 1
        ? this.tabs[currentTabIndex + 1] : this.tabs[0];
    } else {
      const currentTabIndex = this.tabs.map((el) => el.accordionButton)
        .findIndex((tab) => tab === document.activeElement);
      previousTabItem = currentTabIndex - 1 >= 0
        ? this.tabs[currentTabIndex - 1] : this.tabs[this.tabs.length - 1];
      nextTabItem = currentTabIndex + 1 <= this.tabs.length - 1
        ? this.tabs[currentTabIndex + 1] : this.tabs[0];
    }

    // catch left/right and up/down arrow key events
    switch (e.keyCode) {
      case 37:
      case 38:
        if (this.view === 'tabs') {
          previousTabItem.tabButton.click();
          previousTabItem.tabButton.focus();
        } else {
          previousTabItem.accordionButton.focus();
        }
        e.preventDefault();
        break;
      case 39:
      case 40:
        if (this.view === 'tabs') {
          nextTabItem.tabButton.click();
          nextTabItem.tabButton.focus();
        } else {
          nextTabItem.accordionButton.focus();
        }
        e.preventDefault();
        break;
    }
  }

  deactivateTabs() {
    this.tabs.map((tabObj) => {
      tabObj.accordionButton.removeAttribute('aria-disabled');
      tabObj.tabButton.removeAttribute('aria-expanded');
      tabObj.accordionButton.setAttribute('aria-expanded', false);

      if (tabObj.tab.hasAttribute('active')) {
        this.dispatchCustomEvent('joomla.tab.hide', this.view === 'tabs' ? tabObj.tabButton : tabObj.accordionButton, this.previousActive);
        tabObj.tab.removeAttribute('active');
        tabObj.tab.setAttribute('tabindex', '-1');
        // Emit hidden event
        this.dispatchCustomEvent('joomla.tab.hidden', this.view === 'tabs' ? tabObj.tabButton : tabObj.accordionButton, this.previousActive);
        this.previousActive = this.view === 'tabs' ? tabObj.tabButton : tabObj.accordionButton;
      }
      return tabObj;
    });
  }

  activateTab(input, state = true) {
    let currentTrigger;
    if (input.currentTarget) {
      currentTrigger = this.tabs.find((tab) => ((this.view === 'tabs' ? tab.tabButton : tab.accordionButton) === input.currentTarget));
    } else if (input instanceof HTMLElement) {
      currentTrigger = this.tabs.find((tab) => tab.tab === input);
    } else if (Number.isInteger(input)) {
      currentTrigger = this.tabs[input];
    }

    if (currentTrigger) {
      // Accordion can close the active panel
      if (this.view === 'accordion' && this.tabs.find((tab) => tab.accordionButton.getAttribute('aria-expanded') === 'true') === currentTrigger) {
        if (currentTrigger.tab.hasAttribute('active')) {
          currentTrigger.tab.removeAttribute('active');
          return;
        }
        currentTrigger.tab.setAttribute('active', '');
        return;
      }

      // Remove current active
      this.deactivateTabs();
      // Set new active
      currentTrigger.tabButton.setAttribute('aria-expanded', true);
      currentTrigger.accordionButton.setAttribute('aria-expanded', true);
      currentTrigger.accordionButton.setAttribute('aria-disabled', true);
      currentTrigger.tab.setAttribute('active', '');
      currentTrigger.tabButton.removeAttribute('tabindex');
      this.dispatchCustomEvent('joomla.tab.show', this.view === 'tabs' ? currentTrigger.tabButton : currentTrigger.accordionButton, this.previousActive);
      if (state) {
        if (this.view === 'tabs') {
          currentTrigger.tabButton.focus();
        } else {
          currentTrigger.accordionButton.focus();
        }
      }
      if (state) this.saveState(currentTrigger.tab.id);
      this.dispatchCustomEvent('joomla.tab.shown', this.view === 'tabs' ? currentTrigger.tabButton : currentTrigger.accordionButton, this.previousActive);
    }
  }

  // Create navigation elements for inserted tabs
  createNavs(tab) {
    if ((tab instanceof Element && tab.tagName.toLowerCase() !== 'joomla-tab-element') || ![].some.call(this.children, (el) => el === tab).length || !tab.getAttribute('name') || !tab.getAttribute('id')) return;
    const tabs = [].slice.call(this.children).filter((el) => el.tagName.toLowerCase() === 'joomla-tab-element');
    const index = tabs.findIndex((tb) => tb === tab);

    // Create Accordion button
    const accordionButton = document.createElement('button');
    accordionButton.setAttribute('aria-expanded', !!tab.hasAttribute('active'));
    accordionButton.setAttribute('aria-controls', tab.id);
    accordionButton.setAttribute('type', 'button');
    accordionButton.innerHTML = `<span class="accordion-title">${tab.getAttribute('name')}<span class="accordion-icon"></span></span>`;
    tab.insertAdjacentElement('beforebegin', accordionButton);

    if (this.view === 'tabs') {
      accordionButton.setAttribute('hidden', '');
    }

    accordionButton.addEventListener('click', this.activateTab);

    // Create tab button
    const tabButton = document.createElement('button');
    tabButton.setAttribute('aria-expanded', !!tab.hasAttribute('active'));
    tabButton.setAttribute('aria-controls', tab.id);
    tabButton.setAttribute('role', 'tab');
    tabButton.setAttribute('type', 'button');
    tabButton.innerHTML = `${tab.getAttribute('name')}`;
    if (tabs.length - 1 === index) {
      // last
      this.tabButtonContainer.appendChild(tabButton);
      this.tabs.push({
        tab,
        tabButton,
        accordionButton,
      });
    } else if (index === 0) {
      // first
      this.tabButtonContainer.insertAdjacentElement('afterbegin', tabButton);
      this.tabs.slice(0, 0, {
        tab,
        tabButton,
        accordionButton,
      });
    } else {
      // Middle
      this.tabs[index - 1].tabButton.insertAdjacentElement('afterend', tabButton);
      this.tabs.slice(index - 1, 0, {
        tab,
        tabButton,
        accordionButton,
      });
    }

    tabButton.addEventListener('click', this.activateTab);
  }

  // Remove navigation elements for removed tabs
  removeNavs(tab) {
    if ((tab instanceof Element && tab.tagName.toLowerCase() !== 'joomla-tab-element') || ![].some.call(this.children, (el) => el === tab).length || !tab.getAttribute('name') || !tab.getAttribute('id')) return;
    const accordionButton = tab.previousSilbingElement;
    if (accordionButton && accordionButton.tagName.toLowerCase() === 'button') {
      accordionButton.removeEventListener('click', this.keyBehaviour);
      accordionButton.parentNode.removeChild(accordionButton);
    }
    const tabButton = this.tabButtonContainer.querySelector(`[aria-controls=${accordionButton.id}]`);
    if (tabButton) {
      tabButton.removeEventListener('click', this.keyBehaviour);
      tabButton.parentNode.removeChild(tabButton);
    }
    const index = this.tabs.findIndex((tb) => tb.tabs === tab);
    if (index - 1 === 0) {
      this.tabs.shift();
    } else if (index - 1 === this.tabs.length) {
      this.tabs.pop();
    } else {
      this.tabs.splice(index - 1, 1);
    }
  }

  /** Method to convert tabs to accordion and vice versa depending on screen size */
  checkView() {
    if (!this.breakpoint) {
      return;
    }

    if (document.body.getBoundingClientRect().width > this.breakpoint) {
      if (this.view === 'tabs') {
        return;
      }
      this.tabButtonContainer.removeAttribute('hidden');
      this.tabs.map((tab) => {
        tab.accordionButton.setAttribute('hidden', '');
        tab.accordionButton.setAttribute('role', 'tabpanel');
        if (tab.accordionButton.getAttribute('aria-expanded') === 'true') {
          tab.tab.setAttribute('active', '');
        }
        return tab;
      });
      this.setAttribute('view', 'tabs');
    } else {
      if (this.view === 'accordion') {
        return;
      }
      this.tabButtonContainer.setAttribute('hidden', '');
      this.tabs.map((tab) => {
        tab.accordionButton.removeAttribute('hidden');
        tab.accordionButton.setAttribute('role', 'region');
        return tab;
      });
      this.setAttribute('view', 'accordion');
    }
  }

  getStorageKey() {
    return window.location.href.toString().split(window.location.host)[1].replace(/&return=[a-zA-Z0-9%]+/, '').split('#')[0];
  }

  saveState(value) {
    const storageKey = this.getStorageKey();
    sessionStorage.setItem(storageKey, value);
  }

  activateFromState() {
    this.hasNested = this.querySelector('joomla-tab') instanceof HTMLElement;
    // Use the sessionStorage state!
    const href = sessionStorage.getItem(this.getStorageKey());
    if (href) {
      const currentTabIndex = this.tabs.findIndex((tab) => tab.tab.id === href);

      if (currentTabIndex >= 0) {
        this.activateTab(currentTabIndex, false);
      } else if (this.hasNested) {
        const childTabs = this.querySelector('joomla-tab');
        if (childTabs) {
          const activeTabs = [].slice.call(this.querySelectorAll('joomla-tab-element'))
            .reverse()
            .filter((activeTabEl) => activeTabEl.id === href);
          if (activeTabs.length) {
            // Activate the deepest tab
            let activeTab = activeTabs[0].closest('joomla-tab');
            [].slice.call(activeTab.querySelectorAll('joomla-tab-element'))
              .forEach((tabEl) => {
                tabEl.removeAttribute('active');
                if (tabEl.id === href) {
                  tabEl.setAttribute('active', '');
                }
              });

            // Activate all parent tabs
            while (activeTab.parentNode.closest('joomla-tab') !== this) {
              const parentTabContainer = activeTab.closest('joomla-tab');
              const parentTabEl = activeTab.parentNode.closest('joomla-tab-element');
              [].slice.call(parentTabContainer.querySelectorAll('joomla-tab-element'))
                // eslint-disable-next-line no-loop-func
                .forEach((tabEl) => {
                  tabEl.removeAttribute('active');
                  if (parentTabEl === tabEl) {
                    tabEl.setAttribute('active', '');
                    activeTab = parentTabEl;
                  }
                });
            }

            [].slice.call(this.children)
              .filter((el) => el.tagName.toLowerCase() === 'joomla-tab-element')
              .forEach((tabEl) => {
                tabEl.removeAttribute('active');
                const isActiveChild = tabEl.querySelector('joomla-tab-element[active]');

                if (isActiveChild) {
                  this.activateTab(tabEl, false);
                }
              });
          }
        }
      }
    }
  }

  /* Method to dispatch events */
  dispatchCustomEvent(eventName, element, related) {
    const OriginalCustomEvent = new CustomEvent(eventName, { bubbles: true, cancelable: true });
    OriginalCustomEvent.relatedTarget = related;
    element.dispatchEvent(OriginalCustomEvent);
  }
}

customElements.define('joomla-tab', TabsElement);
