/*!
* metismenujs - v1.4.0
* MetisMenu: Collapsible menu plugin with Vanilla-JS
* https://github.com/onokumus/metismenujs#readme
*
* Made by Osman Nuri Okumus <onokumus@gmail.com> (https://github.com/onokumus)
* Under MIT License
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.MetisMenu = factory());
})(this, (function () { 'use strict';

    const Default = {
        parentTrigger: 'li',
        subMenu: 'ul',
        toggle: true,
        triggerElement: 'a',
    };
    const ClassName = {
        ACTIVE: 'mm-active',
        COLLAPSE: 'mm-collapse',
        COLLAPSED: 'mm-collapsed',
        COLLAPSING: 'mm-collapsing',
        METIS: 'metismenu',
        SHOW: 'mm-show',
    };

    /* eslint-disable max-len */
    class MetisMenu {
        /**
         * Creates an instance of MetisMenu.
         *
         * @constructor
         * @param {Element | string} element
         * @param {IMMOptions} [options]
         * @memberof MetisMenu
         */
        constructor(element, options) {
            this.element = MetisMenu.isElement(element) ? element : document.querySelector(element);
            this.config = Object.assign(Object.assign({}, Default), options);
            this.disposed = false;
            this.triggerArr = [];
            this.boundEventHandler = this.clickEvent.bind(this);
            this.init();
        }
        static attach(el, opt) {
            return new MetisMenu(el, opt);
        }
        /**
         * @internal
         */
        init() {
            const { METIS, ACTIVE, COLLAPSE } = ClassName;
            this.element.classList.add(METIS);
            const uls = [...this.element.querySelectorAll(this.config.subMenu)];
            if (uls.length === 0) {
                return;
            }
            uls.forEach((ul) => {
                ul.classList.add(COLLAPSE);
                const li = ul.closest(this.config.parentTrigger);
                if (li === null || li === void 0 ? void 0 : li.classList.contains(ACTIVE)) {
                    this.show(ul);
                }
                else {
                    this.hide(ul);
                }
                const a = li === null || li === void 0 ? void 0 : li.querySelector(this.config.triggerElement);
                if ((a === null || a === void 0 ? void 0 : a.getAttribute("aria-disabled")) === "true") {
                    return;
                }
                a === null || a === void 0 ? void 0 : a.setAttribute("aria-expanded", "false");
                a === null || a === void 0 ? void 0 : a.addEventListener("click", this.boundEventHandler);
                this.triggerArr.push(a);
            });
        }
        /**
         * @internal
         */
        clickEvent(evt) {
            if (!this.disposed) {
                const target = evt === null || evt === void 0 ? void 0 : evt.currentTarget;
                if (target && target.tagName === "A") {
                    evt.preventDefault();
                }
                const li = target.closest(this.config.parentTrigger);
                const ul = li === null || li === void 0 ? void 0 : li.querySelector(this.config.subMenu);
                this.toggle(ul);
            }
        }
        update() {
            this.disposed = false;
            this.init();
        }
        dispose() {
            this.triggerArr.forEach((arr) => {
                arr.removeEventListener("click", this.boundEventHandler);
            });
            this.disposed = true;
        }
        on(evtType, handler, options) {
            this.element.addEventListener(evtType, handler, options);
            return this;
        }
        off(evtType, handler, options) {
            this.element.removeEventListener(evtType, handler, options);
            return this;
        }
        /**
         * @internal
         */
        emit(evtType, evtData, shouldBubble = false) {
            const evt = new CustomEvent(evtType, {
                bubbles: shouldBubble,
                detail: evtData,
            });
            this.element.dispatchEvent(evt);
        }
        /**
         * @internal
         */
        toggle(ul) {
            const li = ul.closest(this.config.parentTrigger);
            if (li === null || li === void 0 ? void 0 : li.classList.contains(ClassName.ACTIVE)) {
                this.hide(ul);
            }
            else {
                this.show(ul);
            }
        }
        /**
         * @internal
         */
        show(el) {
            var _a;
            const ul = el;
            const { ACTIVE, COLLAPSE, COLLAPSED, COLLAPSING, SHOW } = ClassName;
            if (this.isTransitioning || ul.classList.contains(COLLAPSING)) {
                return;
            }
            const complete = () => {
                ul.classList.remove(COLLAPSING);
                ul.style.height = "";
                ul.removeEventListener("transitionend", complete);
                this.setTransitioning(false);
                this.emit("shown.metisMenu", {
                    shownElement: ul,
                });
            };
            const li = ul.closest(this.config.parentTrigger);
            li === null || li === void 0 ? void 0 : li.classList.add(ACTIVE);
            const a = li === null || li === void 0 ? void 0 : li.querySelector(this.config.triggerElement);
            a === null || a === void 0 ? void 0 : a.setAttribute("aria-expanded", "true");
            a === null || a === void 0 ? void 0 : a.classList.remove(COLLAPSED);
            ul.style.height = "0px";
            ul.classList.remove(COLLAPSE);
            ul.classList.remove(SHOW);
            ul.classList.add(COLLAPSING);
            const eleParentSiblins = [].slice.call((_a = li === null || li === void 0 ? void 0 : li.parentNode) === null || _a === void 0 ? void 0 : _a.children).filter((c) => c !== li);
            if (this.config.toggle && eleParentSiblins.length > 0) {
                eleParentSiblins.forEach((sibli) => {
                    const sibUl = sibli.querySelector(this.config.subMenu);
                    if (sibUl) {
                        this.hide(sibUl);
                    }
                });
            }
            this.setTransitioning(true);
            ul.classList.add(COLLAPSE);
            ul.classList.add(SHOW);
            ul.style.height = `${ul.scrollHeight}px`;
            this.emit("show.metisMenu", {
                showElement: ul,
            });
            ul.addEventListener("transitionend", complete);
        }
        /**
         * @internal
         */
        hide(el) {
            const { ACTIVE, COLLAPSE, COLLAPSED, COLLAPSING, SHOW } = ClassName;
            const ul = el;
            if (this.isTransitioning || !ul.classList.contains(SHOW)) {
                return;
            }
            this.emit("hide.metisMenu", {
                hideElement: ul,
            });
            const li = ul.closest(this.config.parentTrigger);
            li === null || li === void 0 ? void 0 : li.classList.remove(ACTIVE);
            const complete = () => {
                ul.classList.remove(COLLAPSING);
                ul.classList.add(COLLAPSE);
                ul.style.height = "";
                ul.removeEventListener("transitionend", complete);
                this.setTransitioning(false);
                this.emit("hidden.metisMenu", {
                    hiddenElement: ul,
                });
            };
            ul.style.height = `${ul.getBoundingClientRect().height}px`;
            ul.style.height = `${ul.offsetHeight}px`;
            ul.classList.add(COLLAPSING);
            ul.classList.remove(COLLAPSE);
            ul.classList.remove(SHOW);
            this.setTransitioning(true);
            ul.addEventListener("transitionend", complete);
            ul.style.height = "0px";
            const a = li === null || li === void 0 ? void 0 : li.querySelector(this.config.triggerElement);
            a === null || a === void 0 ? void 0 : a.setAttribute("aria-expanded", "false");
            a === null || a === void 0 ? void 0 : a.classList.add(COLLAPSED);
        }
        /**
         * @internal
         */
        setTransitioning(isTransitioning) {
            this.isTransitioning = isTransitioning;
        }
        /**
         * @internal
         */
        static isElement(element) {
            return Boolean(element.classList);
        }
    }

    return MetisMenu;

}));
//# sourceMappingURL=metismenujs.js.map
