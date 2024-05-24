/**
 * @package     Joomla.Site
 * @subpackage  Templates.Cassiopeia
 * @copyright   (C) 2017 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0.0
 */Joomla=window.Joomla||{},function(c,e){"use strict";function i(o){for(var t=o&&o.target?o.target:e,l=t.querySelectorAll("fieldset.btn-group"),r=0;r<l.length;r++){var a=l[r];if(a.getAttribute("disabled")===!0){a.style.pointerEvents="none";for(var s=a.querySelectorAll(".btn"),n=0;n<s.length;n++)s[n].classList.add("disabled")}}}e.addEventListener("DOMContentLoaded",function(o){i(o);var t=e.getElementById("back-top");if(t){let l=function(){e.body.scrollTop>20||e.documentElement.scrollTop>20?t.classList.add("visible"):t.classList.remove("visible")};l(),window.onscroll=function(){l()},t.addEventListener("click",function(r){r.preventDefault(),window.scrollTo(0,0)})}[].slice.call(e.head.querySelectorAll('link[rel="lazy-stylesheet"]')).forEach(function(l){l.rel="stylesheet"})}),e.addEventListener("joomla:updated",i)}(Joomla,document);
