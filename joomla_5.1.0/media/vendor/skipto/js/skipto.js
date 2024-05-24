/*! skipto - v4.1.7 - 2023-01-13
* https://github.com/paypal/skipto
* Copyright (c) 2023 PayPal Accessibility Team and University of Illinois; Licensed  */
 /*@cc_on @*/
/*@if (@_jscript_version >= 5.8) @*/
/* ========================================================================
* Copyright (c) <2021> PayPal and University of Illinois
* All rights reserved.
* Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* Neither the name of PayPal or any of its subsidiaries or affiliates nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
* ======================================================================== */

(function() {
  'use strict';
  var SkipTo = {
    skipToId: 'id-skip-to-js-4',
    skipToMenuId: 'id-skip-to-menu-4',
    domNode: null,
    buttonNode: null,
    menuNode: null,
    tooltipNode: null,
    menuitemNodes: [],
    firstMenuitem: false,
    lastMenuitem: false,
    firstChars: [],
    headingLevels: [],
    skipToIdIndex: 1,
    showAllLandmarksSelector: 'main, [role=main], [role=search], nav, [role=navigation], section[aria-label], section[aria-labelledby], section[title], [role=region][aria-label], [role=region][aria-labelledby], [role=region][title], form[aria-label], form[aria-labelledby], aside, [role=complementary], body > header, [role=banner], body > footer, [role=contentinfo]',
    showAllHeadingsSelector: 'h1, h2, h3, h4, h5, h6',
    showTooltipFocus: false,
    showTooltipHover: false,
    tooltipTimerDelay: 500,  // in milliseconds
    // Default configuration values
    config: {
      // Feature switches
      enableActions: false,
      enableMofN: true,
      enableHeadingLevelShortcuts: true,
      enableHelp: true,
      enableTooltip: true,
      // Customization of button and menu
      accesskey: '0', // default is the number zero
      attachElement: 'header',
      displayOption: 'static', // options: static (default), popup
      // container element, use containerClass for custom styling
      containerElement: 'div',
      containerRole: '',
      customClass: '',

      // Button labels and messages
      buttonTitle: '',   // deprecated in favor of buttonTooltip
      buttonTitleWithAccesskey: '',  // deprecated in favor of buttonTooltipAccesskey
      buttonTooltip: '',
      buttonTooltipAccesskey: 'Shortcut Key: $key',
      buttonLabel: 'Skip To Content',

      // Menu labels and messages
      menuLabel: 'Landmarks and Headings',
      landmarkGroupLabel: 'Landmarks',
      headingGroupLabel: 'Headings',
      mofnGroupLabel: ' ($m of $n)',
      headingLevelLabel: 'Heading level',
      mainLabel: 'main',
      searchLabel: 'search',
      navLabel: 'navigation',
      regionLabel: 'region',
      asideLabel: 'complementary',
      footerLabel: 'contentinfo',
      headerLabel: 'banner',
      formLabel: 'form',
      msgNoLandmarksFound: 'No landmarks found',
      msgNoHeadingsFound: 'No headings found',

      // Action labels and messages
      actionGroupLabel: 'Actions',
      actionShowHeadingsHelp: 'Toggles between showing "All" and "Selected" Headings.',
      actionShowSelectedHeadingsLabel: 'Show Selected Headings ($num)',
      actionShowAllHeadingsLabel: 'Show All Headings ($num)',
      actionShowLandmarksHelp: 'Toggles between showing "All" and "Selected" Landmarks.',
      actionShowSelectedLandmarksLabel: 'Show Selected Landmarks ($num)',
      actionShowAllLandmarksLabel: 'Show All Landmarks ($num)',

      actionShowSelectedHeadingsAriaLabel: 'Show $num selected headings',
      actionShowAllHeadingsAriaLabel: 'Show all $num headings',
      actionShowSelectedLandmarksAriaLabel: 'Show $num selected landmarks',
      actionShowAllLandmarksAriaLabel: 'Show all $num landmarks',

      // Selectors for landmark and headings sections
      landmarks: 'main, [role="main"], [role="search"], nav, [role="navigation"], aside, [role="complementary"]',
      headings: 'main h1, [role="main"] h1, main h2, [role="main"] h2',

      // Custom CSS position and colors
      colorTheme: '',
      fontFamily: '',
      fontSize: '',
      positionLeft: '',
      menuTextColor: '',
      menuBackgroundColor: '',
      menuitemFocusTextColor: '',
      menuitemFocusBackgroundColor: '',
      focusBorderColor: '',
      buttonTextColor: '',
      buttonBackgroundColor: '',
    },
    colorThemes: {
      'default': {
        fontFamily: 'inherit',
        fontSize: 'inherit',
        positionLeft: '46%',
        menuTextColor: '#1a1a1a',
        menuBackgroundColor: '#dcdcdc',
        menuitemFocusTextColor: '#eeeeee',
        menuitemFocusBackgroundColor: '#1a1a1a',
        focusBorderColor: '#1a1a1a',
        buttonTextColor: '#1a1a1a',
        buttonBackgroundColor: '#eeeeee',
      },
      'illinois': {
        fontFamily: 'inherit',
        fontSize: 'inherit',
        positionLeft: '46%',
        menuTextColor: '#00132c',
        menuBackgroundColor: '#cad9ef',
        menuitemFocusTextColor: '#eeeeee',
        menuitemFocusBackgroundColor: '#00132c',
        focusBorderColor: '#ff552e',
        buttonTextColor: '#444444',
        buttonBackgroundColor: '#dddede',
      },
      'aria': {
        fontFamily: 'sans-serif',
        fontSize: '10pt',
        positionLeft: '7%',
        menuTextColor: '#000',
        menuBackgroundColor: '#def',
        menuitemFocusTextColor: '#fff',
        menuitemFocusBackgroundColor: '#005a9c',
        focusBorderColor: '#005a9c',
        buttonTextColor: '#005a9c',
        buttonBackgroundColor: '#ddd',
      }
    },
    defaultCSS: '.skip-to.popup{position:absolute;top:-30em;left:0}.skip-to,.skip-to.popup.focus{position:absolute;top:0;left:$positionLeft;font-family:$fontFamily;font-size:$fontSize}.skip-to.fixed{position:fixed}.skip-to button{position:relative;margin:0;padding:6px 8px 6px 8px;border-width:0 1px 1px 1px;border-style:solid;border-radius:0 0 6px 6px;border-color:$buttonBackgroundColor;color:$menuTextColor;background-color:$buttonBackgroundColor;z-index:200;font-family:$fontFamily;font-size:$fontSize}.skip-to .skip-to-tooltip{position:absolute;top:2.25em;left:8em;margin:1px;padding:4px;border:1px solid #ccc;box-shadow:2px 3px 5px #ddd;background-color:#eee;color:#000;font-family:Helvetica,Arial,Sans-Serif;font-variant-numeric:slashed-zero;font-size:9pt;width:auto;display:none;white-space:nowrap;z-index:201}.skip-to .skip-to-tooltip.skip-to-show-tooltip{display:block}.skip-to [aria-expanded=true]+.skip-to-tooltip.skip-to-show-tooltip{display:none}.skip-to [role=menu]{position:absolute;min-width:17em;display:none;margin:0;padding:.25rem;background-color:$menuBackgroundColor;border-width:2px;border-style:solid;border-color:$focusBorderColor;border-radius:5px;z-index:1000}.skip-to [role=group]{display:grid;grid-auto-rows:min-content;grid-row-gap:1px}.skip-to [role=separator]:first-child{border-radius:5px 5px 0 0}.skip-to [role=menuitem]{padding:3px;width:auto;border-width:0;border-style:solid;color:$menuTextColor;background-color:$menuBackgroundColor;z-index:1000;display:grid;overflow-y:auto;grid-template-columns:repeat(6,1.2rem) 1fr;grid-column-gap:2px;font-size:1em}.skip-to [role=menuitem] .label,.skip-to [role=menuitem] .level{font-size:100%;font-weight:400;color:$menuTextColor;display:inline-block;background-color:$menuBackgroundColor;line-height:inherit;display:inline-block}.skip-to [role=menuitem] .level{text-align:right;padding-right:4px}.skip-to [role=menuitem] .label{text-align:left;margin:0;padding:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}[dir=rtl] .skip-to [role=menuitem] .label{text-align:right}.skip-to [role=menuitem] .label:first-letter,.skip-to [role=menuitem] .level:first-letter{text-decoration:underline;text-transform:uppercase}.skip-to [role=menuitem].skip-to-h1 .level{grid-column:1}.skip-to [role=menuitem].skip-to-h2 .level{grid-column:2}.skip-to [role=menuitem].skip-to-h3 .level{grid-column:3}.skip-to [role=menuitem].skip-to-h4 .level{grid-column:4}.skip-to [role=menuitem].skip-to-h5 .level{grid-column:5}.skip-to [role=menuitem].skip-to-h6 .level{grid-column:8}.skip-to [role=menuitem].skip-to-h1 .label{grid-column:2/8}.skip-to [role=menuitem].skip-to-h2 .label{grid-column:3/8}.skip-to [role=menuitem].skip-to-h3 .label{grid-column:4/8}.skip-to [role=menuitem].skip-to-h4 .label{grid-column:5/8}.skip-to [role=menuitem].skip-to-h5 .label{grid-column:6/8}.skip-to [role=menuitem].skip-to-h6 .label{grid-column:7/8}.skip-to [role=menuitem].skip-to-h1.no-level .label{grid-column:1/8}.skip-to [role=menuitem].skip-to-h2.no-level .label{grid-column:2/8}.skip-to [role=menuitem].skip-to-h3.no-level .label{grid-column:3/8}.skip-to [role=menuitem].skip-to-h4.no-level .label{grid-column:4/8}.skip-to [role=menuitem].skip-to-h5.no-level .label{grid-column:5/8}.skip-to [role=menuitem].skip-to-h6.no-level .label{grid-column:6/8}.skip-to [role=menuitem].skip-to-nesting-level-1 .nesting{grid-column:1}.skip-to [role=menuitem].skip-to-nesting-level-2 .nesting{grid-column:2}.skip-to [role=menuitem].skip-to-nesting-level-3 .nesting{grid-column:3}.skip-to [role=menuitem].skip-to-nesting-level-0 .label{grid-column:1/8}.skip-to [role=menuitem].skip-to-nesting-level-1 .label{grid-column:2/8}.skip-to [role=menuitem].skip-to-nesting-level-2 .label{grid-column:3/8}.skip-to [role=menuitem].skip-to-nesting-level-3 .label{grid-column:4/8}.skip-to [role=menuitem].action .label,.skip-to [role=menuitem].no-items .label{grid-column:1/8}.skip-to [role=separator]{margin:1px 0 1px 0;padding:3px;display:block;width:auto;font-weight:700;border-bottom-width:1px;border-bottom-style:solid;border-bottom-color:$menuTextColor;background-color:$menuBackgroundColor;color:$menuTextColor;z-index:1000}.skip-to [role=separator] .mofn{font-weight:400;font-size:85%}.skip-to [role=separator]:first-child{border-radius:5px 5px 0 0}.skip-to [role=menuitem].last{border-radius:0 0 5px 5px}.skip-to.focus{display:block}.skip-to button:focus,.skip-to button:hover{background-color:$menuBackgroundColor;color:$menuTextColor;outline:0}.skip-to button:focus{padding:6px 7px 5px 7px;border-width:0 2px 2px 2px;border-color:$focusBorderColor}.skip-to [role=menuitem]:focus{padding:1px;border-width:2px;border-style:solid;border-color:$focusBorderColor;background-color:$menuitemFocusBackgroundColor;color:$menuitemFocusTextColor;outline:0}.skip-to [role=menuitem]:focus .label,.skip-to [role=menuitem]:focus .level{background-color:$menuitemFocusBackgroundColor;color:$menuitemFocusTextColor}',

    //
    // Functions related to configuring the features
    // of skipTo
    //
    isNotEmptyString: function(str) {
      return (typeof str === 'string') && str.length && str.trim() && str !== "&nbsp;";
    },
    isEmptyString: function(str) {
      return (typeof str !== 'string') || str.length === 0 && !str.trim();
    },
    init: function(config) {
      var node;
      // Check if skipto is already loaded

      if (document.querySelector('style#' + this.skipToId)) {
        return;
      }

      var attachElement = document.body;
      if (config) {
        this.setUpConfig(config);
      }
      if (typeof this.config.attachElement === 'string') {
        node = document.querySelector(this.config.attachElement);
        if (node && node.nodeType === Node.ELEMENT_NODE) {
          attachElement = node;
        }
      }
      this.addCSSColors();
      this.renderStyleElement(this.defaultCSS);
      var elem = this.config.containerElement.toLowerCase().trim();
      if (!this.isNotEmptyString(elem)) {
        elem = 'div';
      }
      this.domNode = document.createElement(elem);
      this.domNode.classList.add('skip-to');
      if (this.isNotEmptyString(this.config.customClass)) {
        this.domNode.classList.add(this.config.customClass);
      }
      if (this.isNotEmptyString(this.config.containerRole)) {
        this.domNode.setAttribute('role', this.config.containerRole);
      }
      var displayOption = this.config.displayOption;
      if (typeof displayOption === 'string') {
        displayOption = displayOption.trim().toLowerCase();
        if (displayOption.length) {
          switch (this.config.displayOption) {
            case 'fixed':
              this.domNode.classList.add('fixed');
              break;
            case 'onfocus':  // Legacy option
            case 'popup':
              this.domNode.classList.add('popup');
              break;
            default:
              break;
          }
        }
      }
      // Place skip to at the beginning of the document
      if (attachElement.firstElementChild) {
        attachElement.insertBefore(this.domNode, attachElement.firstElementChild);
      } else {
        attachElement.appendChild(this.domNode);
      }
      this.buttonNode = document.createElement('button');
      this.buttonNode.textContent = this.config.buttonLabel;
      this.buttonNode.setAttribute('aria-haspopup', 'true');
      this.buttonNode.setAttribute('aria-expanded', 'false');
      this.buttonNode.setAttribute('aria-controls', this.skipToMenuId);
      this.buttonNode.setAttribute('accesskey', this.config.accesskey);

      this.domNode.appendChild(this.buttonNode);

      this.renderTooltip(this.domNode, this.buttonNode);

      this.menuNode = document.createElement('div');
      this.menuNode.setAttribute('role', 'menu');
      this.menuNode.setAttribute('aria-busy', 'true');
      this.menuNode.setAttribute('id', this.skipToMenuId);
      this.domNode.appendChild(this.menuNode);
      this.buttonNode.addEventListener('keydown', this.handleButtonKeydown.bind(this));
      this.buttonNode.addEventListener('click', this.handleButtonClick.bind(this));
      this.buttonNode.addEventListener('focus', this.handleButtonFocus.bind(this));
      this.buttonNode.addEventListener('blur', this.handleButtonBlur.bind(this));
      this.buttonNode.addEventListener('pointerenter', this.handleButtonPointerenter.bind(this));
      this.buttonNode.addEventListener('pointerout', this.handleButtonPointerout.bind(this));
      this.domNode.addEventListener('focusin', this.handleFocusin.bind(this));
      this.domNode.addEventListener('focusout', this.handleFocusout.bind(this));
      window.addEventListener('pointerdown', this.handleBackgroundPointerdown.bind(this), true);

    },
    renderTooltip: function(attachNode, buttonNode) {
      var id = 'id-skip-to-tooltip';
      var accesskey = this.getBrowserSpecificAccesskey(this.config.accesskey);

      var tooltip = this.config.buttonTooltip;
      // for backward compatibility, support 'this.config.buttonTitle' if defined
      if (this.isNotEmptyString(this.config.buttonTitle)) {
        tooltip = this.config.buttonTitle;
      }

      this.tooltipLeft = buttonNode.getBoundingClientRect().width;
      this.tooltipTop  = buttonNode.getBoundingClientRect().height;

      this.tooltipNode = document.createElement('div');
      this.tooltipNode.setAttribute('role', 'tooltip');
      this.tooltipNode.id = id;
      this.tooltipNode.classList.add('skip-to-tooltip');

      if (this.isNotEmptyString(accesskey)) {
        tooltip = this.config.buttonTooltipAccesskey.replace('$key', accesskey);
        // for backward compatibility support 'buttonTitleWithAccesskey' if defined
        if (this.isNotEmptyString(this.config.buttonTitleWithAccesskey)) {
          tooltip = this.config.buttonTitleWithAccesskey.replace('$key', accesskey);
        }
      }

      if (this.isEmptyString(tooltip)) {
        // if there is no tooltip information
        // do not display tooltip
        this.config.enableTooltip = false;
      } else {
        this.tooltipNode.textContent = tooltip;
      }

      attachNode.appendChild(this.tooltipNode);
      this.tooltipNode.style.left = this.tooltipLeft + 'px';
      this.tooltipNode.style.top = this.tooltipTop + 'px';

      // Temporarily show the tooltip to get rendered height
      this.tooltipNode.classList.add('skip-to-show-tooltip');
      this.tooltipHeight = this.tooltipNode.getBoundingClientRect().height;
      this.tooltipNode.classList.remove('skip-to-show-tooltip');
    },

    updateStyle: function(stylePlaceholder, value, defaultValue) {
      if (typeof value !== 'string' || value.length === 0) {
        value = defaultValue;
      }
      var index1 = this.defaultCSS.indexOf(stylePlaceholder);
      var index2 = index1 + stylePlaceholder.length;
      while (index1 >= 0 && index2 < this.defaultCSS.length) {
        this.defaultCSS = this.defaultCSS.substring(0, index1) + value + this.defaultCSS.substring(index2);
        index1 = this.defaultCSS.indexOf(stylePlaceholder, index2);
        index2 = index1 + stylePlaceholder.length;
      }
    },
    addCSSColors: function() {
      var theme = this.colorThemes['default'];
      if (typeof this.colorThemes[this.config.colorTheme] === 'object') {
        theme = this.colorThemes[this.config.colorTheme];
      }
      this.updateStyle('$fontFamily', this.config.fontFamily, theme.fontFamily);
      this.updateStyle('$fontSize', this.config.fontSize, theme.fontSize);

      this.updateStyle('$positionLeft', this.config.positionLeft, theme.positionLeft);

      this.updateStyle('$menuTextColor', this.config.menuTextColor, theme.menuTextColor);
      this.updateStyle('$menuBackgroundColor', this.config.menuBackgroundColor, theme.menuBackgroundColor);

      this.updateStyle('$menuitemFocusTextColor', this.config.menuitemFocusTextColor, theme.menuitemFocusTextColor);
      this.updateStyle('$menuitemFocusBackgroundColor', this.config.menuitemFocusBackgroundColor, theme.menuitemFocusBackgroundColor);

      this.updateStyle('$focusBorderColor', this.config.focusBorderColor, theme.focusBorderColor);

      this.updateStyle('$buttonTextColor', this.config.buttonTextColor, theme.buttonTextColor);
      this.updateStyle('$buttonBackgroundColor', this.config.buttonBackgroundColor, theme.buttonBackgroundColor);
    },

    getBrowserSpecificAccesskey: function (accesskey) {
      var userAgent = navigator.userAgent.toLowerCase();
      var platform =  navigator.platform.toLowerCase();

      var hasWin    = platform.indexOf('win') >= 0;
      var hasMac    = platform.indexOf('mac') >= 0;
      var hasLinux  = platform.indexOf('linux') >= 0 || platform.indexOf('bsd') >= 0;

      var hasAndroid = userAgent.indexOf('android') >= 0;
      var hasFirefox = userAgent.indexOf('firefox') >= 0;
      var hasChrome = userAgent.indexOf('chrome') >= 0;
      var hasOpera = userAgent.indexOf('opr') >= 0;

      if (typeof accesskey !== 'string' || accesskey.length === 0) {
        return '';
      }

      if (hasWin || (hasLinux && !hasAndroid)) {
        if (hasFirefox) {
          return "Shift + Alt + " + accesskey;
        } else {
          if (hasChrome || hasOpera) {
            return "Alt + " + accesskey;
          }
        }
      }

      if (hasMac) {
        return "Ctrl + Option + " + accesskey;
      }

      return '';
    },
    setUpConfig: function(appConfig) {
      var localConfig = this.config,
        name,
        appConfigSettings = typeof appConfig.settings !== 'undefined' ? appConfig.settings.skipTo : {};
      for (name in appConfigSettings) {
        //overwrite values of our local config, based on the external config
        if ((typeof localConfig[name] !== 'undefined') &&
           ((typeof appConfigSettings[name] === 'string') &&
            (appConfigSettings[name].length > 0 ) ||
           typeof appConfigSettings[name] === 'boolean')
          ) {
          localConfig[name] = appConfigSettings[name];
        } else {
          throw new Error('** SkipTo Problem with user configuration option "' + name + '".');
        }
      }
    },
    renderStyleElement: function(cssString) {
      var styleNode = document.createElement('style');
      var headNode = document.getElementsByTagName('head')[0];
      var css = document.createTextNode(cssString);

      styleNode.setAttribute("type", "text/css");
      // ID is used to test whether skipto is already loaded
      styleNode.id = this.skipToId;
      styleNode.appendChild(css);
      headNode.appendChild(styleNode);
    },

    //
    // Functions related to creating and populating the
    // the popup menu
    //

    getFirstChar: function(menuitem) {
      var c = '';
      var label = menuitem.querySelector('.label');
      if (label && this.isNotEmptyString(label.textContent)) {
        c = label.textContent.trim()[0].toLowerCase();
      }
      return c;
    },

    getHeadingLevelFromAttribute: function(menuitem) {
      var level = '';
      if (menuitem.hasAttribute('data-level')) {
        level = menuitem.getAttribute('data-level');
      }
      return level;
    },

    updateKeyboardShortCuts: function () {
      var mi;
      this.firstChars = [];
      this.headingLevels = [];

      for(var i = 0; i < this.menuitemNodes.length; i += 1) {
        mi = this.menuitemNodes[i];
        this.firstChars.push(this.getFirstChar(mi));
        this.headingLevels.push(this.getHeadingLevelFromAttribute(mi));
      }
    },

    updateMenuitems: function () {
      var menuitemNodes = this.menuNode.querySelectorAll('[role=menuitem');

      this.menuitemNodes = [];
      for(var i = 0; i < menuitemNodes.length; i += 1) {
        this.menuitemNodes.push(menuitemNodes[i]);
      }

      this.firstMenuitem = this.menuitemNodes[0];
      this.lastMenuitem = this.menuitemNodes[this.menuitemNodes.length-1];
      this.lastMenuitem.classList.add('last');
      this.updateKeyboardShortCuts();
    },

    renderMenuitemToGroup: function (groupNode, mi) {
      var tagNode, tagNodeChild, labelNode, nestingNode;

      var menuitemNode = document.createElement('div');
      menuitemNode.setAttribute('role', 'menuitem');
      menuitemNode.classList.add(mi.class);
      if (this.isNotEmptyString(mi.tagName)) {
        menuitemNode.classList.add('skip-to-' + mi.tagName.toLowerCase());
      }
      menuitemNode.setAttribute('data-id', mi.dataId);
      menuitemNode.tabIndex = -1;
      if (this.isNotEmptyString(mi.ariaLabel)) {
        menuitemNode.setAttribute('aria-label', mi.ariaLabel);
      }

      // add event handlers
      menuitemNode.addEventListener('keydown', this.handleMenuitemKeydown.bind(this));
      menuitemNode.addEventListener('click', this.handleMenuitemClick.bind(this));
      menuitemNode.addEventListener('pointerenter', this.handleMenuitemPointerenter.bind(this));

      groupNode.appendChild(menuitemNode);

      // add heading level and label
      if (mi.class.includes('heading')) {
        if (this.config.enableHeadingLevelShortcuts) {
          tagNode = document.createElement('span');
          tagNodeChild = document.createElement('span');
          tagNodeChild.appendChild(document.createTextNode(mi.level));
          tagNode.append(tagNodeChild);
          tagNode.appendChild(document.createTextNode(')'));
          tagNode.classList.add('level');
          menuitemNode.append(tagNode);
        } else {
          menuitemNode.classList.add('no-level');
        }
        menuitemNode.setAttribute('data-level', mi.level);
        if (this.isNotEmptyString(mi.tagName)) {
          menuitemNode.classList.add('skip-to-' + mi.tagName);
        }
      }

      // add nesting level for landmarks
      if (mi.class.includes('landmark')) {
        menuitemNode.setAttribute('data-nesting', mi.nestingLevel);
        menuitemNode.classList.add('skip-to-nesting-level-' + mi.nestingLevel);

        if (mi.nestingLevel > 0 && (mi.nestingLevel > this.lastNestingLevel)) {
          nestingNode = document.createElement('span');
          nestingNode.classList.add('nesting');
          menuitemNode.append(nestingNode);
        }
        this.lastNestingLevel = mi.nestingLevel;
      }

      labelNode = document.createElement('span');
      labelNode.appendChild(document.createTextNode(mi.name));
      labelNode.classList.add('label');
      menuitemNode.append(labelNode);

      return menuitemNode;
    },

    renderGroupLabel: function (groupLabelId, title, m, n) {
      var titleNode, mofnNode, s;
      var groupLabelNode = document.getElementById(groupLabelId);

      titleNode = groupLabelNode.querySelector('.title');
      mofnNode = groupLabelNode.querySelector('.mofn');

      titleNode.textContent = title;

      if (this.config.enableActions && this.config.enableMofN) {
        if ((typeof m === 'number') && (typeof n === 'number')) {
          s = this.config.mofnGroupLabel;
          s = s.replace('$m', m);
          s = s.replace('$n', n);
          mofnNode.textContent = s;
        }
      }
    },

    renderMenuitemGroup: function(groupId, title) {
      var labelNode, groupNode, spanNode;
      var menuNode = this.menuNode;
      if (this.isNotEmptyString(title)) {
        labelNode = document.createElement('div');
        labelNode.id = groupId + "-label";
        labelNode.setAttribute('role', 'separator');
        menuNode.appendChild(labelNode);

        spanNode = document.createElement('span');
        spanNode.classList.add('title');
        spanNode.textContent = title;
        labelNode.append(spanNode);

        spanNode = document.createElement('span');
        spanNode.classList.add('mofn');
        labelNode.append(spanNode);

        groupNode = document.createElement('div');
        groupNode.setAttribute('role', 'group');
        groupNode.setAttribute('aria-labelledby', labelNode.id);
        groupNode.id = groupId;
        menuNode.appendChild(groupNode);
        menuNode = groupNode;
      }
      return groupNode;
    },

    removeMenuitemGroup: function(groupId) {
      var node = document.getElementById(groupId);
      this.menuNode.removeChild(node);
      node = document.getElementById(groupId + "-label");
      this.menuNode.removeChild(node);
    },

    renderMenuitemsToGroup: function(groupNode, menuitems, msgNoItemsFound) {
    groupNode.innerHTML = '';
    this.lastNestingLevel = 0;

    if (menuitems.length === 0) {
        var item = {};
        item.name = msgNoItemsFound;
        item.tagName = '';
        item.class = 'no-items';
        item.dataId = '';
        this.renderMenuitemToGroup(groupNode, item);
    }
    else {
        for (var i = 0; i < menuitems.length; i += 1) {
        this.renderMenuitemToGroup(groupNode, menuitems[i]);
        }
    }
},

    getShowMoreHeadingsSelector: function(option) {
      if (option === 'all') {
        return this.showAllHeadingsSelector;
      }
      return this.config.headings;
    },

    getShowMoreHeadingsLabel: function(option, n) {
      var label = this.config.actionShowSelectedHeadingsLabel;
      if (option === 'all') {
        label = this.config.actionShowAllHeadingsLabel;
      }
      return label.replace('$num', n);
    },

    getShowMoreHeadingsAriaLabel: function(option, n) {
      var label = this.config.actionShowSelectedHeadingsAriaLabel;

      if (option === 'all') {
        label = this.config.actionShowAllHeadingsAriaLabel;
      }

      return label.replace('$num', n);
    },

    renderActionMoreHeadings: function(groupNode) {
      var item, menuitemNode;
      var option = 'all';

      var selectedHeadingsLen = this.getHeadings(this.getShowMoreHeadingsSelector('selected')).length;
      var allHeadingsLen = this.getHeadings(this.getShowMoreHeadingsSelector('all')).length;
      var noAction = selectedHeadingsLen === allHeadingsLen;
      var headingsLen = allHeadingsLen;

      if (option !== 'all') {
        headingsLen = selectedHeadingsLen;
      }

      if (!noAction) {
        item = {};
        item.tagName = '';
        item.role = 'menuitem';
        item.class = 'action';
        item.dataId = 'skip-to-more-headings';
        item.name = this.getShowMoreHeadingsLabel(option, headingsLen);
        item.ariaLabel = this.getShowMoreHeadingsAriaLabel(option, headingsLen);

        menuitemNode = this.renderMenuitemToGroup(groupNode, item);
        menuitemNode.setAttribute('data-show-heading-option', option);
        menuitemNode.title = this.config.actionShowHeadingsHelp;
      }
      return noAction;
    },

    updateHeadingGroupMenuitems: function(option) {
      var headings, headingsLen, labelNode, groupNode;

      var selectedHeadings = this.getHeadings(this.getShowMoreHeadingsSelector('selected'));
      var selectedHeadingsLen = selectedHeadings.length;
      var allHeadings = this.getHeadings(this.getShowMoreHeadingsSelector('all'));
      var allHeadingsLen = allHeadings.length;

      // Update list of headings
      if ( option === 'all' ) {
        headings = allHeadings;
      }
      else {
        headings = selectedHeadings;
      }

      this.renderGroupLabel('id-skip-to-group-headings-label', this.config.headingGroupLabel, headings.length, allHeadings.length);

      groupNode = document.getElementById('id-skip-to-group-headings');
      this.renderMenuitemsToGroup(groupNode, headings, this.config.msgNoHeadingsFound);
      this.updateMenuitems();

      // Move focus to first heading menuitem
      if (groupNode.firstElementChild) {
        groupNode.firstElementChild.focus();
      }

      // Update heading action menuitem
      if (option === 'all') {
        option = 'selected';
        headingsLen = selectedHeadingsLen;
      } else {
        option = 'all';
        headingsLen = allHeadingsLen;
      }

      var menuitemNode = this.menuNode.querySelector('[data-id=skip-to-more-headings]');
      menuitemNode.setAttribute('data-show-heading-option', option);
      menuitemNode.setAttribute('aria-label', this.getShowMoreHeadingsAriaLabel(option, headingsLen));

      labelNode = menuitemNode.querySelector('span.label');
      labelNode.textContent = this.getShowMoreHeadingsLabel(option, headingsLen);
    },

    getShowMoreLandmarksSelector: function(option) {
      if (option === 'all') {
        return this.showAllLandmarksSelector;
      }
      return this.config.landmarks;
    },

    getShowMoreLandmarksLabel: function(option, n) {
      var label = this.config.actionShowSelectedLandmarksLabel;

      if (option === 'all') {
        label = this.config.actionShowAllLandmarksLabel;
      }
      return label.replace('$num', n);
    },

    getShowMoreLandmarksAriaLabel: function(option, n) {
      var label = this.config.actionShowSelectedLandmarksAriaLabel;

      if (option === 'all') {
        label = this.config.actionShowAllLandmarksAriaLabel;
      }

      return label.replace('$num', n);
    },

    renderActionMoreLandmarks: function(groupNode) {
      var item, menuitemNode;
      var option = 'all';

      var selectedLandmarksLen = this.getLandmarks(this.getShowMoreLandmarksSelector('selected')).length;
      var allLandmarksLen = this.getLandmarks(this.getShowMoreLandmarksSelector('all')).length;
      var noAction = selectedLandmarksLen === allLandmarksLen;
      var landmarksLen = allLandmarksLen;

      if (option !== 'all') {
        landmarksLen = selectedLandmarksLen;
      }

      if (!noAction) {
        item = {};
        item.tagName = '';
        item.role = 'menuitem';
        item.class = 'action';
        item.dataId = 'skip-to-more-landmarks';
        item.name = this.getShowMoreLandmarksLabel(option, landmarksLen);
        item.ariaLabel =  this.getShowMoreLandmarksAriaLabel(option, landmarksLen);

        menuitemNode = this.renderMenuitemToGroup(groupNode, item);

        menuitemNode.setAttribute('data-show-landmark-option', option);
        menuitemNode.title = this.config.actionShowLandmarksHelp;
      }
      return noAction;
    },

    updateLandmarksGroupMenuitems: function(option) {
      var landmarks, landmarksLen, labelNode, groupNode;
      var selectedLandmarks = this.getLandmarks(this.getShowMoreLandmarksSelector('selected'));
      var selectedLandmarksLen = selectedLandmarks.length;
      var allLandmarks = this.getLandmarks(this.getShowMoreLandmarksSelector('all'), true);
      var allLandmarksLen = allLandmarks.length;

      // Update landmark menu items
      if ( option === 'all' ) {
        landmarks = allLandmarks;
      }
      else {
        landmarks = selectedLandmarks;
      }

      this.renderGroupLabel('id-skip-to-group-landmarks-label', this.config.landmarkGroupLabel, landmarks.length, allLandmarks.length);

      groupNode = document.getElementById('id-skip-to-group-landmarks');
      this.renderMenuitemsToGroup(groupNode, landmarks, this.config.msgNoLandmarksFound);
      this.updateMenuitems();

      // Move focus to first landmark menuitem
      if (groupNode.firstElementChild) {
        groupNode.firstElementChild.focus();
      }

      // Update landmark action menuitem
      if (option === 'all') {
        option = 'selected';
        landmarksLen = selectedLandmarksLen;
      } else {
        option = 'all';
        landmarksLen = allLandmarksLen;
      }

      var menuitemNode = this.menuNode.querySelector('[data-id=skip-to-more-landmarks]');
      menuitemNode.setAttribute('data-show-landmark-option', option);
      menuitemNode.setAttribute('aria-label', this.getShowMoreLandmarksAriaLabel(option, landmarksLen));

      labelNode = menuitemNode.querySelector('span.label');
      labelNode.textContent = this.getShowMoreLandmarksLabel(option, landmarksLen);
    },

    renderMenu: function() {
      var groupNode,
      selectedLandmarks,
      allLandmarks,
      landmarkElements,
      selectedHeadings,
      allHeadings,
      headingElements,
      selector,
      option,
      hasNoAction1,
      hasNoAction2;
      // remove current menu items from menu
      while (this.menuNode.lastElementChild) {
        this.menuNode.removeChild(this.menuNode.lastElementChild);
      }

      option = 'selected';
      // Create landmarks group
      selector = this.getShowMoreLandmarksSelector('all');
      allLandmarks = this.getLandmarks(selector, true);
      selector = this.getShowMoreLandmarksSelector('selected');
      selectedLandmarks = this.getLandmarks(selector);
      landmarkElements = selectedLandmarks;

      if (option === 'all') {
        landmarkElements = allLandmarks;
      }

      groupNode = this.renderMenuitemGroup('id-skip-to-group-landmarks', this.config.landmarkGroupLabel);
      this.renderMenuitemsToGroup(groupNode, landmarkElements, this.config.msgNoLandmarksFound);
      this.renderGroupLabel('id-skip-to-group-landmarks-label', this.config.landmarkGroupLabel, landmarkElements.length, allLandmarks.length);

      // Create headings group
      selector = this.getShowMoreHeadingsSelector('all');
      allHeadings = this.getHeadings(selector);
      selector = this.getShowMoreHeadingsSelector('selected');
      selectedHeadings = this.getHeadings(selector);
      headingElements = selectedHeadings;

      if (option === 'all') {
        headingElements = allHeadings;
      }

      groupNode = this.renderMenuitemGroup('id-skip-to-group-headings', this.config.headingGroupLabel);
      this.renderMenuitemsToGroup(groupNode, headingElements, this.config.msgNoHeadingsFound);
      this.renderGroupLabel('id-skip-to-group-headings-label', this.config.headingGroupLabel, headingElements.length, allHeadings.length);

      // Create actions, if enabled
      if (this.config.enableActions) {
        groupNode = this.renderMenuitemGroup('id-skip-to-group-actions', this.config.actionGroupLabel);
        hasNoAction1 = this.renderActionMoreLandmarks(groupNode);
        hasNoAction2 = this.renderActionMoreHeadings(groupNode);
        // Remove action label if no actions are available
        if (hasNoAction1 && hasNoAction2) {
          this.removeMenuitemGroup('id-skip-to-group-actions');
        }
      }

      // Update list of menuitems
      this.updateMenuitems();
    },

    //
    // Menu scripting event functions and utilities
    //

    setFocusToMenuitem: function(menuitem) {
      if (menuitem) {
        menuitem.focus();
      }
    },

    setFocusToFirstMenuitem: function() {
      this.setFocusToMenuitem(this.firstMenuitem);
    },

    setFocusToLastMenuitem: function() {
      this.setFocusToMenuitem(this.lastMenuitem);
    },

    setFocusToPreviousMenuitem: function(menuitem) {
      var newMenuitem, index;
      if (menuitem === this.firstMenuitem) {
        newMenuitem = this.lastMenuitem;
      } else {
        index = this.menuitemNodes.indexOf(menuitem);
        newMenuitem = this.menuitemNodes[index - 1];
      }
      this.setFocusToMenuitem(newMenuitem);
      return newMenuitem;
    },

    setFocusToNextMenuitem: function(menuitem) {
      var newMenuitem, index;
      if (menuitem === this.lastMenuitem) {
        newMenuitem = this.firstMenuitem;
      } else {
        index = this.menuitemNodes.indexOf(menuitem);
        newMenuitem = this.menuitemNodes[index + 1];
      }
      this.setFocusToMenuitem(newMenuitem);
      return newMenuitem;
    },

    setFocusByFirstCharacter: function(menuitem, char) {
      var start, index;
      if (char.length > 1) {
        return;
      }
      char = char.toLowerCase();

      // Get start index for search based on position of currentItem
      start = this.menuitemNodes.indexOf(menuitem) + 1;
      if (start >= this.menuitemNodes.length) {
        start = 0;
      }

      // Check remaining items in the menu
      index = this.firstChars.indexOf(char, start);

      // If not found in remaining items, check headings
      if (index === -1) {
        index = this.headingLevels.indexOf(char, start);
      }

      // If not found in remaining items, check from beginning
      if (index === -1) {
        index = this.firstChars.indexOf(char, 0);
      }

      // If not found in remaining items, check headings from beginning
      if (index === -1) {
        index = this.headingLevels.indexOf(char, 0);
      }

      // If match was found...
      if (index > -1) {
        this.setFocusToMenuitem(this.menuitemNodes[index]);
      }
    },

    // Utilities
    getIndexFirstChars: function(startIndex, char) {
      for (var i = startIndex; i < this.firstChars.length; i += 1) {
        if (char === this.firstChars[i]) {
          return i;
        }
      }
      return -1;
    },
    // Popup menu methods
    openPopup: function() {
      this.menuNode.setAttribute('aria-busy', 'true');
      this.renderMenu();
      this.menuNode.style.display = 'block';
      this.menuNode.removeAttribute('aria-busy');
      this.buttonNode.setAttribute('aria-expanded', 'true');
    },

    closePopup: function() {
      if (this.isOpen()) {
        this.buttonNode.setAttribute('aria-expanded', 'false');
        this.menuNode.style.display = 'none';
      }
    },
    isOpen: function() {
      return this.buttonNode.getAttribute('aria-expanded') === 'true';
    },
    // Menu event handlers
    handleFocusin: function() {
      this.domNode.classList.add('focus');
    },
    handleFocusout: function() {
      this.domNode.classList.remove('focus');
    },
    handleButtonKeydown: function(event) {
      var key = event.key,
        flag = false;
      switch (key) {
        case ' ':
        case 'Enter':
        case 'ArrowDown':
        case 'Down':
          this.openPopup();
          this.setFocusToFirstMenuitem();
          flag = true;
          break;
        case 'Esc':
        case 'Escape':
          this.closePopup();
          this.buttonNode.focus();
          this.hideTooltip();
          flag = true;
          break;
        case 'Up':
        case 'ArrowUp':
          this.openPopup();
          this.setFocusToLastMenuitem();
          flag = true;
          break;
        default:
          break;
      }
      if (flag) {
        event.stopPropagation();
        event.preventDefault();
      }
    },
    handleButtonClick: function(event) {
      if (this.isOpen()) {
        this.closePopup();
        this.buttonNode.focus();
      } else {
        this.openPopup();
        this.setFocusToFirstMenuitem();
      }
      event.stopPropagation();
      event.preventDefault();
    },
    isTooltipHidden: function() {
      return this.tooltipNode.className.indexOf('skip-to-show-tooltip') < 0;
    },
    displayTooltip: function() {
      if (this.showTooltipFocus || this.showTooltipHover) {
        this.tooltipNode.classList.add('skip-to-show-tooltip');
      }
    },
    showTooltip: function() {
      this.showTooltipFocus = true;
      if (this.config.enableTooltip && this.isTooltipHidden()) {
        this.tooltipNode.style.left = this.tooltipLeft + 'px';
        this.tooltipNode.style.top = this.tooltipTop + 'px';
        setTimeout(this.displayTooltip.bind(this), this.tooltipTimerDelay);
      }
    },
    hideTooltip: function() {
      this.showTooltipFocus = false;
      if(this.config.enableTooltip) {
        this.tooltipNode.classList.remove('skip-to-show-tooltip');
      }
    },
    handleButtonFocus: function() {
      this.showTooltip();
    },
    handleButtonBlur: function() {
      this.hideTooltip();
    },
    handleButtonPointerenter: function(event) {
      this.showTooltipHover = true;
      if (this.config.enableTooltip && this.isTooltipHidden()) {
        var rect = this.buttonNode.getBoundingClientRect();
        var left = Math.min(this.tooltipLeft, event.pageX - rect.left + this.tooltipHeight);
        this.tooltipNode.style.left = left + 'px';
        var top = event.pageY - rect.top;
        this.tooltipNode.style.top = top + 'px';
        setTimeout(this.showTooltip. bind(this), this.tooltipTimerDelay);
      }
    },
    handleButtonPointerout: function() {
      this.showTooltipHover = false;
      if(this.config.enableTooltip) {
        this.tooltipNode.classList.remove('skip-to-show-tooltip');
      }
    },
    skipToElement: function(menuitem) {

      var isVisible = this.isVisible;
      var focusNode = false;
      var scrollNode = false;
      var elem;

      function findVisibleElement(e, selectors) {
        if (e) {
          for (var j = 0; j < selectors.length; j += 1) {
            var elems = e.querySelectorAll(selectors[j]);
            for(var i = 0; i < elems.length; i +=1) {
              if (isVisible(elems[i])) {
                return elems[i];
              }
            }
          }
        }
        return e;
      }

      var searchSelectors = ['input', 'button', 'input[type=button]', 'input[type=submit]', 'a'];
      var navigationSelectors = ['a', 'input', 'button', 'input[type=button]', 'input[type=submit]'];
      var landmarkSelectors = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'section', 'article', 'p', 'li', 'a'];

      var isLandmark = menuitem.classList.contains('landmark');
      var isSearch = menuitem.classList.contains('skip-to-search');
      var isNav = menuitem.classList.contains('skip-to-nav');

      elem = document.querySelector('[data-skip-to-id="' + menuitem.getAttribute('data-id') + '"]');

      if (elem) {
        if (isSearch) {
          focusNode = findVisibleElement(elem, searchSelectors);
        }
        if (isNav) {
          focusNode = findVisibleElement(elem, navigationSelectors);
        }
        if (focusNode && this.isVisible(focusNode)) {
          focusNode.focus();
          focusNode.scrollIntoView({block: 'nearest'});
        }
        else {
          if (isLandmark) {
            scrollNode = findVisibleElement(elem, landmarkSelectors);
            if (scrollNode) {
              elem = scrollNode;
            }
          }
          elem.tabIndex = -1;
          elem.focus();
          elem.scrollIntoView({block: 'center'});
        }
      }
    },
    handleMenuitemAction: function(tgt) {
      var option;
      switch (tgt.getAttribute('data-id')) {
        case '':
          // this means there were no headings or landmarks in the list
          break;

        case 'skip-to-more-headings':
          option = tgt.getAttribute('data-show-heading-option');
          this.updateHeadingGroupMenuitems(option);
          break;

        case 'skip-to-more-landmarks':
          option = tgt.getAttribute('data-show-landmark-option');
          this.updateLandmarksGroupMenuitems(option);
          break;

        default:
          this.closePopup();
          this.skipToElement(tgt);
          break;
      }
    },
    handleMenuitemKeydown: function(event) {
      var tgt = event.currentTarget,
        key = event.key,
        flag = false;

      function isPrintableCharacter(str) {
        return str.length === 1 && str.match(/\S/);
      }
      if (event.ctrlKey || event.altKey || event.metaKey) {
        return;
      }
      if (event.shiftKey) {
        if (isPrintableCharacter(key)) {
          this.setFocusByFirstCharacter(tgt, key);
          flag = true;
        }
        if (event.key === 'Tab') {
          this.buttonNode.focus();
          this.closePopup();
          flag = true;
        }
      } else {
        switch (key) {
          case 'Enter':
          case ' ':
            this.handleMenuitemAction(tgt);
            flag = true;
            break;
          case 'Esc':
          case 'Escape':
            this.closePopup();
            this.buttonNode.focus();
            flag = true;
            break;
          case 'Up':
          case 'ArrowUp':
            this.setFocusToPreviousMenuitem(tgt);
            flag = true;
            break;
          case 'ArrowDown':
          case 'Down':
            this.setFocusToNextMenuitem(tgt);
            flag = true;
            break;
          case 'Home':
          case 'PageUp':
            this.setFocusToFirstMenuitem();
            flag = true;
            break;
          case 'End':
          case 'PageDown':
            this.setFocusToLastMenuitem();
            flag = true;
            break;
          case 'Tab':
            this.closePopup();
            break;
          default:
            if (isPrintableCharacter(key)) {
              this.setFocusByFirstCharacter(tgt, key);
              flag = true;
            }
            break;
        }
      }
      if (flag) {
        event.stopPropagation();
        event.preventDefault();
      }
    },
    handleMenuitemClick: function(event) {
      this.handleMenuitemAction(event.currentTarget);
      event.stopPropagation();
      event.preventDefault();
    },
    handleMenuitemPointerenter: function(event) {
      var tgt = event.currentTarget;
      tgt.focus();
    },
    handleBackgroundPointerdown: function(event) {
      if (!this.domNode.contains(event.target)) {
        if (this.isOpen()) {
          this.closePopup();
          this.buttonNode.focus();
        }
      }
    },
    // methods to extract landmarks, headings and ids
    normalizeName: function(name) {
      if (typeof name === 'string') return name.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
      });
      return "";
    },
    getTextContent: function(elem) {
      function getText(e, strings) {
        // If text node get the text and return
        if (e.nodeType === Node.TEXT_NODE) {
          strings.push(e.data);
        } else {
          // if an element for through all the children elements looking for text
          if (e.nodeType === Node.ELEMENT_NODE) {
            // check to see if IMG or AREA element and to use ALT content if defined
            var tagName = e.tagName.toLowerCase();
            if ((tagName === 'img') || (tagName === 'area')) {
              if (e.alt) {
                strings.push(e.alt);
              }
            } else {
              var c = e.firstChild;
              while (c) {
                getText(c, strings);
                c = c.nextSibling;
              } // end loop
            }
          }
        }
      } // end function getStrings
      // Create return object
      var str = "Test",
        strings = [];
      getText(elem, strings);
      if (strings.length) str = strings.join(" ");
      return str;
    },
    getAccessibleName: function(elem) {
      var labelledbyIds = elem.getAttribute('aria-labelledby'),
        label = elem.getAttribute('aria-label'),
        title = elem.getAttribute('title'),
        name = "";
      if (labelledbyIds && labelledbyIds.length) {
        var str,
          strings = [],
          ids = labelledbyIds.split(' ');
        if (!ids.length) ids = [labelledbyIds];
        for (var i = 0, l = ids.length; i < l; i += 1) {
          var e = document.getElementById(ids[i]);
          if (e) str = this.getTextContent(e);
          if (str && str.length) strings.push(str);
        }
        name = strings.join(" ");
      } else {
        if (this.isNotEmptyString(label)) {
          name = label;
        } else {
          if (this.isNotEmptyString(title)) {
            name = title;
          }
        }
      }
      return name;
    },
    isVisible: function(element) {
      function isVisibleRec(el) {
        if (el.nodeType === 9) return true; /*IE8 does not support Node.DOCUMENT_NODE*/
        var computedStyle = window.getComputedStyle(el);
        var display = computedStyle.getPropertyValue('display');
        var visibility = computedStyle.getPropertyValue('visibility');
        var hidden = el.getAttribute('hidden');
        if ((display === 'none') ||
          (visibility === 'hidden') ||
          (hidden !== null)) {
          return false;
        }
        return isVisibleRec(el.parentNode);
      }
      return isVisibleRec(element);
    },
    getHeadings: function(targets) {
      var dataId, level;
      if (typeof targets !== 'string') {
        targets = this.config.headings;
      }
      var headingElementsArr = [];
      if (typeof targets !== 'string' || targets.length === 0) return;
      var headings = document.querySelectorAll(targets);
      for (var i = 0, len = headings.length; i < len; i += 1) {
        var heading = headings[i];
        var role = heading.getAttribute('role');
        if ((typeof role === 'string') && (role === 'presentation')) continue;
        if (this.isVisible(heading) && this.isNotEmptyString(heading.innerHTML)) {
          if (heading.hasAttribute('data-skip-to-id')) {
            dataId = heading.getAttribute('data-skip-to-id');
          } else {
            heading.setAttribute('data-skip-to-id', this.skipToIdIndex);
            dataId = this.skipToIdIndex;
          }
          level = heading.tagName.substring(1);
          var headingItem = {};
          headingItem.dataId = dataId.toString();
          headingItem.class = 'heading';
          headingItem.name = this.getTextContent(heading);
          headingItem.ariaLabel = headingItem.name + ', ';
          headingItem.ariaLabel += this.config.headingLevelLabel + ' ' + level;
          headingItem.tagName = heading.tagName.toLowerCase();
          headingItem.role = 'heading';
          headingItem.level = level;
          headingElementsArr.push(headingItem);
          this.skipToIdIndex += 1;
        }
      }
      return headingElementsArr;
    },
    getLocalizedLandmarkName: function(tagName, name) {
      var n;
      switch (tagName) {
        case 'aside':
          n = this.config.asideLabel;
          break;
        case 'footer':
          n = this.config.footerLabel;
          break;
        case 'form':
          n = this.config.formLabel;
          break;
        case 'header':
          n = this.config.headerLabel;
          break;
        case 'main':
          n = this.config.mainLabel;
          break;
        case 'nav':
          n = this.config.navLabel;
          break;
        case 'section':
        case 'region':
          n = this.config.regionLabel;
          break;
        case 'search':
          n = this.config.searchLabel;
          break;
          // When an ID is used as a selector, assume for main content
        default:
          n = tagName;
          break;
      }
      if (this.isNotEmptyString(name)) {
        n += ': ' + name;
      }
      return n;
    },
    getNestingLevel: function(landmark, landmarks) {
      var nestingLevel = 0;
      var parentNode = landmark.parentNode;
      while (parentNode) {
        for (var i = 0; i < landmarks.length; i += 1) {
          if (landmarks[i] === parentNode) {
            nestingLevel += 1;
            // no more than 3 levels of nesting supported
            if (nestingLevel === 3) {
              return 3;
            }
            continue;
          }
        }
        parentNode = parentNode.parentNode;
      }
      return nestingLevel;
    },
    getLandmarks: function(targets, allFlag) {
      if (typeof allFlag !== 'boolean') {
        allFlag = false;
      }
      if (typeof targets !== 'string') {
        targets = this.config.landmarks;
      }
      var landmarks = document.querySelectorAll(targets);
      var mainElements = [];
      var searchElements = [];
      var navElements = [];
      var asideElements = [];
      var footerElements = [];
      var regionElements = [];
      var otherElements = [];
      var allLandmarks = [];
      var dataId = '';
      for (var i = 0, len = landmarks.length; i < len; i += 1) {
        var landmark = landmarks[i];
        // if skipto is a landmark don't include it in the list
        if (landmark === this.domNode) {
          continue;
        }
        var role = landmark.getAttribute('role');
        var tagName = landmark.tagName.toLowerCase();
        if ((typeof role === 'string') && (role === 'presentation')) continue;
        if (this.isVisible(landmark)) {
          if (!role) role = tagName;
          var name = this.getAccessibleName(landmark);
          if (typeof name !== 'string') {
            name = '';
          }
          // normalize tagNames
          switch (role) {
            case 'banner':
              tagName = 'header';
              break;
            case 'complementary':
              tagName = 'aside';
              break;
            case 'contentinfo':
              tagName = 'footer';
              break;
            case 'form':
              tagName = 'form';
              break;
            case 'main':
              tagName = 'main';
              break;
            case 'navigation':
              tagName = 'nav';
              break;
            case 'region':
              tagName = 'section';
              break;
            case 'search':
              tagName = 'search';
              break;
            default:
              break;
          }
          // if using ID for selectQuery give tagName as main
          if (['aside', 'footer', 'form', 'header', 'main', 'nav', 'section', 'search'].indexOf(tagName) < 0) {
            tagName = 'main';
          }
          if (landmark.hasAttribute('aria-roledescription')) {
            tagName = landmark.getAttribute('aria-roledescription').trim().replace(' ', '-');
          }
          if (landmark.hasAttribute('data-skip-to-id')) {
            dataId = landmark.getAttribute('data-skip-to-id');
          } else {
            landmark.setAttribute('data-skip-to-id', this.skipToIdIndex);
            dataId =  this.skipToIdIndex;
          }
          var landmarkItem = {};
          landmarkItem.dataId = dataId.toString();
          landmarkItem.class = 'landmark';
          landmarkItem.hasName = name.length > 0;
          landmarkItem.name = this.getLocalizedLandmarkName(tagName, name);
          landmarkItem.tagName = tagName;
          landmarkItem.nestingLevel = 0;
          if (allFlag) {
            landmarkItem.nestingLevel = this.getNestingLevel(landmark, landmarks);
          }
          this.skipToIdIndex += 1;
          allLandmarks.push(landmarkItem);

          // For sorting landmarks into groups
          switch (tagName) {
            case 'main':
              mainElements.push(landmarkItem);
              break;
            case 'search':
              searchElements.push(landmarkItem);
              break;
            case 'nav':
              navElements.push(landmarkItem);
              break;
            case 'aside':
              asideElements.push(landmarkItem);
              break;
            case 'footer':
              footerElements.push(landmarkItem);
              break;
            case 'section':
              // Regions must have accessible name to be included
              if (landmarkItem.hasName) {
                regionElements.push(landmarkItem);
              }
              break;
            default:
              otherElements.push(landmarkItem);
              break;
          }
        }
      }
      if (allFlag) {
        return allLandmarks;
      }
      return [].concat(mainElements, searchElements, navElements, asideElements, regionElements, footerElements, otherElements);
    }
  };
  // Initialize skipto menu button with onload event
  window.addEventListener('load', function() {
    SkipTo.init(window.SkipToConfig ||
                ((typeof window.Joomla === 'object' && typeof window.Joomla.getOptions === 'function') ? window.Joomla.getOptions('skipto-settings', {}) : {})
                );
  });
})();
/*@end @*/
