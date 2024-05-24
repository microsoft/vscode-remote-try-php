/**
 * @package     Joomla.JavaScript
 * @copyright   (C) 2018 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */((c,s)=>{c.JoomlainitReCaptcha2=()=>{const o=[].slice.call(s.getElementsByClassName("g-recaptcha")),r=["sitekey","theme","size","tabindex","callback","expired-callback","error-callback"];o.forEach(t=>{let a={};t.dataset?a=t.dataset:r.forEach(e=>{const i=`data-${e}`;t.hasAttribute(i)&&(a[e]=t.getAttribute(i))}),t.setAttribute("data-recaptcha-widget-id",c.grecaptcha.render(t,a))})}})(window,document);
