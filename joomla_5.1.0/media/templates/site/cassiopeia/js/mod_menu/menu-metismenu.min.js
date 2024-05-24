/**
 * @package     Joomla.Site
 * @subpackage  Templates.cassiopeia
 * @copyright   (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0.0
 */document.addEventListener("DOMContentLoaded",()=>{document.querySelectorAll("ul.mod-menu_dropdown-metismenu").forEach(n=>{const t=new MetisMenu(n,{triggerElement:"button.mm-toggler"}).on("shown.metisMenu",e=>{window.addEventListener("click",function o(i){e.target.contains(i.target)||(t.hide(e.detail.shownElement),window.removeEventListener("click",o))})})})});
