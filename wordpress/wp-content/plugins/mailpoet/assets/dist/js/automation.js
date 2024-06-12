"use strict";(self.webpackChunk=self.webpackChunk||[]).push([[916],{33916:function(t,e,a){var o={};a.r(o),a.d(o,{deleteAutomation:function(){return D},duplicateAutomation:function(){return L},loadAutomations:function(){return C},restoreAutomation:function(){return U},trashAutomation:function(){return M}});var i={};a.r(i),a.d(i,{deleteLegacyAutomation:function(){return z},loadLegacyAutomations:function(){return G},restoreLegacyAutomation:function(){return q},trashLegacyAutomation:function(){return W}});var n={};a.r(n),a.d(n,{getAllAutomations:function(){return J},getAutomationCount:function(){return K},getAutomations:function(){return Q},getLegacyAutomations:function(){return V}});var s=a(70079),r=a(2226),l=a(26952),u=a(44259),m=a(485),c=a(21229),d=a(89912),p=a(96854),_=a(74325);const h="mailpoet/automation-listing";var g=a(96082),y=a(43160),f=a(33934),A=a(69138),x=a(51867),j=a(30550),b=a(26462),T=a(21263),w=a(28234),v=a(69177),E=a(35250);function N({automation:t,label:e,variant:a="link"}){const{id:o,isLegacy:i}=t;return i?(0,E.jsx)(T.ZP,{variant:a,href:`?page=mailpoet-newsletter-editor&id=${o}`,children:e??(0,x.__)("Edit","mailpoet")}):(0,E.jsx)(T.ZP,{variant:a,href:(0,w.f)(v.MailPoet.urls.automationEditor,{id:t.id}),children:e??(0,x.__)("Edit","mailpoet")})}var S=a(14029);function k({automation:t,previousStatus:e}){const{restoreAutomation:a}=(0,S.Z)(h);return(0,E.jsx)(T.ZP,{variant:"link",onClick:()=>a(t,e),children:(0,x.__)("Undo","mailpoet")})}const P=(t,e)=>(0,A.W)(j.store).createSuccessNotice(t,e),O=t=>(0,A.W)(j.store).removeNotice(t);function*C(){return{type:"SET_AUTOMATIONS",automations:(yield(0,f.apiFetch)({path:"/automations"})).data}}function*L(t){const e=yield(0,f.apiFetch)({path:`/automations/${t.id}/duplicate`,method:"POST"});return P((0,x.sprintf)((0,x.__)('Automation "%s" was duplicated.',"mailpoet"),t.name)),{type:"ADD_AUTOMATION",automation:e.data}}function*M(t){const e=yield(0,f.apiFetch)({path:`/automations/${t.id}`,method:"PUT",data:{status:b.b.TRASH}}),a=(0,x.sprintf)((0,x.__)('Automation "%s" was moved to the trash.',"mailpoet"),t.name);return P(a,{id:`automation-trashed-${t.id}`,__unstableHTML:(0,E.jsxs)("p",{children:[a," ",(0,E.jsx)(k,{automation:t,previousStatus:t.status})]})}),{type:"UPDATE_AUTOMATION",automation:e.data}}function*U(t,e){const a=yield(0,f.apiFetch)({path:`/automations/${t.id}`,method:"PUT",data:{status:e}});O(`automation-trashed-${t.id}`);const o=(0,x.sprintf)((0,x.__)('Automation "%s" was restored from the trash.',"mailpoet"),t.name);return P(o,{__unstableHTML:(0,E.jsxs)("p",{children:[o," ",(0,E.jsx)(N,{automation:t,label:(0,x.__)("Edit automation","mailpoet")})]})}),{type:"UPDATE_AUTOMATION",automation:a.data}}function*D(t){return yield(0,f.apiFetch)({path:`/automations/${t.id}`,method:"DELETE"}),P((0,x.sprintf)((0,x.__)('Automation "%s" and all associated data were permanently deleted.',"mailpoet"),t.name)),{type:"DELETE_AUTOMATION",automation:t}}var I=a(689);const Z=window.mailpoet_roles||{},$=window.mailpoet_segments||[],F=window.mailpoet_woocommerce_automatic_emails||{},H=t=>{const e="welcome"===t.type?(t=>{const e=t.options;if("user"===e.event)return"mailpoet_all"===e.role?(0,x.__)("Sent when a new WordPress user is added to your site.","mailpoet"):(0,x.sprintf)((0,x.__)("Sent when a new WordPress user with the role %s is added to your site.","mailpoet"),Z[e.role]);const a=Object.values($).find((({id:t})=>Number(t)===Number(e.segment)));return void 0===a?(0,E.jsx)("a",{href:`/send/${t.id}`,children:(0,x.__)("You need to select a list to send to.","mailpoet")}):(0,x.sprintf)((0,x.__)("Sent when someone subscribes to the list: %s.","mailpoet"),a.name)})(t):(t=>{const e=t.options,a=F[e.group].events[e.event];let o;try{o=JSON.parse(e.meta??null)}catch(t){o=e.meta??null}const i=o&&o.option&&Array.isArray(o.option)?o.option.map((({name:t})=>t)):[];if(o&&0===i.length)return(0,E.jsx)("span",{className:"mailpoet-listing-error",children:(0,x.__)("You need to configure email options before this email can be sent.","mailpoet")});const n=i.length>1&&"listingScheduleDisplayTextPlural"in a?a.listingScheduleDisplayTextPlural:a.listingScheduleDisplayText;return(0,x.sprintf)(n.endsWith(".")?n:`${n}.`,i.join(", "))})(t),a=e?(t=>{const e=t.options;if("immediate"===e.afterTimeType)return;const a=e.afterTimeNumber;switch(e.afterTimeType){case"minutes":return(0,x.sprintf)((0,x.__)("%d minute(s) later","mailpoet"),a);case"hours":return(0,x.sprintf)((0,x.__)("%d hour(s) later","mailpoet"),a);case"days":return(0,x.sprintf)((0,x.__)("%d day(s) later","mailpoet"),a);case"weeks":return(0,x.sprintf)((0,x.__)("%d week(s) later","mailpoet"),a);default:return(0,x.__)("Invalid sending delay.","mailpoet")}})(t):void 0;return e?(0,E.jsxs)(E.Fragment,{children:[e,a&&(0,E.jsxs)(E.Fragment,{children:[" ",a]})]}):void 0},R=(t,e)=>(0,A.W)(j.store).createSuccessNotice(t,e),Y=t=>(0,A.W)(j.store).removeNotice(t),B=t=>({id:t.id,name:t.subject,status:t.deleted_at?b.b.TRASH:t.status,stats:{totals:{entered:t.total_scheduled+t.total_sent,in_progress:t.total_scheduled,exited:t.total_sent}},isLegacy:!0,description:H(t)});function*G(){return{type:"SET_LEGACY_AUTOMATIONS",automations:(yield(0,f.__unstableAwaitPromise)(Promise.all([(0,I.E)({endpoint:"newsletters",method:"listing","data[params][type]":"welcome","data[limit]":"400"}),(0,I.E)({endpoint:"newsletters",method:"listing","data[params][type]":"automatic","data[limit]":"400"})]))).flatMap((({data:t})=>t)).map(B)}}function*W(t){return yield(0,f.__unstableAwaitPromise)((0,I.E)({endpoint:"newsletters",method:"trash","data[id]":`${t.id}`})),R((0,x.sprintf)((0,x.__)('Automation "%s" was moved to the trash.',"mailpoet"),t.name)),{type:"UPDATE_LEGACY_AUTOMATION_STATUS",id:t.id,status:b.b.TRASH}}function*q(t){const e=yield(0,f.__unstableAwaitPromise)((0,I.E)({endpoint:"newsletters",method:"restore","data[id]":`${t.id}`}));return Y(`automation-trashed-${t.id}`),R((0,x.sprintf)((0,x.__)('Automation "%s" was restored from the trash.',"mailpoet"),t.name)),{type:"UPDATE_LEGACY_AUTOMATION_STATUS",id:t.id,status:e.data.status}}function*z(t){return yield(0,f.__unstableAwaitPromise)((0,I.E)({endpoint:"newsletters",method:"delete","data[id]":`${t.id}`})),R((0,x.sprintf)((0,x.__)('Automation "%s" and all associated data were permanently deleted.',"mailpoet"),t.name)),{type:"DELETE_LEGACY_AUTOMATION",id:t.id}}function Q(t){return t.automations}function V(t){return t.legacyAutomations}function J(t){return t.automations&&t.legacyAutomations?[...t.automations,...t.legacyAutomations]:void 0}function K(t){return t.automations&&t.legacyAutomations?t.automations.length+t.legacyAutomations.length:_.automationCount+_.legacyAutomationCount}var X=a(81922),tt=a(83200),et=a(91285),at=a(33493),ot=a(33584),it=a(14275),nt=a(33791);const st=nt.Z,rt=t=>{const[e,a]=(0,s.useState)(!1),{deleteAutomation:o,deleteLegacyAutomation:i}=(0,S.Z)(h);if(t.status!==b.b.TRASH)return;const n=t.isLegacy?i:o;return{key:"delete",control:{title:(0,x.__)("Delete permanently","mailpoet"),icon:null,onClick:()=>a(!0)},slot:(0,E.jsx)(st,{isOpen:e,title:(0,x.__)("Permanently delete automation","mailpoet"),confirmButtonText:(0,x.__)("Yes, permanently delete","mailpoet"),__experimentalHideHeader:!1,onConfirm:()=>n(t),onCancel:()=>a(!1),children:(0,x.sprintf)((0,x.__)('Are you sure you want to permanently delete "%s" and all associated data? This cannot be undone!',"mailpoet"),t.name)})}},lt=t=>{const{restoreAutomation:e,restoreLegacyAutomation:a}=(0,S.Z)(h),o=t.isLegacy?a:e;if(t.status===b.b.TRASH)return{key:"restore",control:{title:(0,x.__)("Restore","mailpoet"),icon:null,onClick:()=>{o(t,b.b.DRAFT)}}}},ut=nt.Z,mt=t=>{const[e,a]=(0,s.useState)(!1),{trashAutomation:o,trashLegacyAutomation:i}=(0,S.Z)(h);if(t.status===b.b.TRASH)return;const n=t.isLegacy?i:o;return{key:"trash",control:{title:(0,x._x)("Trash","verb","mailpoet"),icon:null,onClick:()=>a(!0)},slot:(0,E.jsx)(ut,{isOpen:e,title:(0,x.__)("Trash automation","mailpoet"),confirmButtonText:(0,x.__)("Yes, move to trash","mailpoet"),__experimentalHideHeader:!1,onConfirm:()=>n(t),onCancel:()=>a(!1),children:(0,x.sprintf)((0,x.__)('Are you sure you want to move the automation "%s" to the Trash?',"mailpoet"),t.name)})}};function ct({automation:t,label:e,variant:a="link"}){const{id:o,isLegacy:i}=t;return i?(0,E.jsx)(T.ZP,{variant:a,href:`?page=mailpoet-newsletters&context=automation#/stats/${o}`,children:e??(0,x.__)("Analytics","mailpoet")}):(0,E.jsx)(T.ZP,{variant:a,href:(0,w.f)(v.MailPoet.urls.automationAnalytics,{id:t.id}),children:e??(0,x.__)("Analytics","mailpoet")})}function dt({automation:t}){const e=[mt(t),lt(t),rt(t)].filter((t=>t));return(0,E.jsxs)("div",{className:"mailpoet-listing-actions-cell",children:[(0,E.jsx)(ct,{automation:t,variant:"tertiary"}),(0,E.jsx)(N,{automation:t,variant:"tertiary"}),e.map((({control:t,slot:e})=>(0,E.jsx)(s.Fragment,{children:e},t.title))),(0,E.jsx)(ot.Z,{className:"mailpoet-listing-more-button",label:(0,x.__)("More","mailpoet"),icon:it.Z,controls:e.map((({control:t})=>t)),popoverProps:{placement:"bottom-start"}})]})}function pt({automation:t}){return(0,E.jsxs)(E.Fragment,{children:[(0,E.jsx)(N,{automation:t,label:t.name}),t.description&&(0,E.jsx)("div",{children:t.description})]})}function _t({automation:t}){let e="";switch(t.status){case b.b.ACTIVE:e=(0,x.__)("Active","mailpoet");break;case b.b.DEACTIVATING:e=(0,x.__)("Deactivating","mailpoet");break;default:e=(0,x.__)("Draft","mailpoet")}return(0,E.jsx)("div",{className:"mailpoet-automation-listing-cell-status",children:e})}var ht=a(12711);function gt({automation:t}){return(0,E.jsx)(ht.g,{labelPosition:"after",items:[{key:"entered",label:(0,x._x)("Entered","automation stats","mailpoet"),value:t.stats.totals.entered},{key:"processing",label:(0,x._x)("Processing","automation stats","mailpoet"),value:t.stats.totals.in_progress},{key:"exited",label:(0,x._x)("Exited","automation stats","mailpoet"),value:t.stats.totals.exited}]})}var yt=a(2632);const ft=[{key:"name",label:(0,x.__)("Name","mailpoet"),cellClassName:"mailpoet-listing-name mailpoet-automation-listing-cell-name"},{key:"subscribers",label:(0,x.__)("Subscribers","mailpoet")},{key:"status",label:(0,x.__)("Status","mailpoet")},{key:"actions"}];function At(){return(0,E.jsx)(yt.m,{heading:(0,x.__)("Automations","mailpoet"),children:(0,E.jsx)(T.ZP,{href:v.MailPoet.urls.automationTemplates,icon:at.R,variant:"primary",className:"mailpoet-add-new-button",children:(0,x.__)("New automation","mailpoet")})})}function xt(){const t=(0,et.k6)(),e=(0,et.TH)(),a=(0,s.useMemo)((()=>new URLSearchParams(e.search)),[e]),o=(0,c.Z)((t=>t(h).getAllAutomations())),{loadAutomations:i,loadLegacyAutomations:n}=(0,S.Z)(h),r=a.get("status");(0,s.useEffect)((()=>{i(),n()}),[i,n]);const l=(0,s.useCallback)((e=>{const o=new URLSearchParams({...Object.fromEntries(a.entries()),...e}),i=Object.keys(e);(i.includes("status")||i.includes("per_page")||"1"===o.get("paged"))&&o.delete("paged"),t.push({search:o.toString()})}),[a,t]),u=(0,s.useMemo)((()=>{const t={all:[]};return(o??[]).forEach((e=>{t[e.status]||(t[e.status]=[]),t[e.status].push(e),e.status!==b.b.TRASH&&t.all.push(e)})),t}),[o]),m=(0,s.useMemo)((()=>[{name:"all",title:(0,x.__)("All","mailpoet"),className:"mailpoet-tab-all"},{name:b.b.ACTIVE,title:(0,x.__)("Active","mailpoet"),className:"mailpoet-tab-active"},{name:b.b.DRAFT,title:(0,x._x)("Draft","noun","mailpoet"),className:"mailpoet-tab-draft"},{name:b.b.TRASH,title:(0,x._x)("Trash","noun","mailpoet"),className:"mailpoet-tab-trash"}].map((t=>{const e=(u[t.name]??[]).length;return{name:t.name,title:(0,E.jsxs)(E.Fragment,{children:[(0,E.jsx)("span",{children:t.title}),e>0&&(0,E.jsx)("span",{className:"count",children:e})]}),className:t.className}}))),[u]),d=(0,s.useCallback)((t=>{const e=_.automationCount+_.legacyAutomationCount,i=u[t.name]??[],n=parseInt(a.get("per_page")??"25",10),s=(parseInt(a.get("paged")??"1",10)-1)*n,r=i.map((t=>function(t){return[{id:t.id,value:t.name,display:(0,E.jsx)(pt,{automation:t})},{id:t.id,value:null,display:(0,E.jsx)(gt,{automation:t})},{id:t.id,value:t.status,display:(0,E.jsx)(_t,{automation:t})},{id:t.id,value:null,display:(0,E.jsx)(dt,{automation:t})}]}(t))).slice(s,s+n);return(0,E.jsx)(X.Z,{className:"mailpoet-listing-card mailpoet-automation-listing",title:"",isLoading:!o,headers:ft,rows:r,rowKey:(t,e)=>i[e].id*(i[e].isLegacy?-1:1),rowsPerPage:n,onQueryChange:t=>e=>{l({[t]:e})},totalRows:o?i.length:e,query:Object.fromEntries(a),showMenu:!1})}),[o,u,a,l]);return(0,E.jsx)(tt.Z,{className:"mailpoet-filter-tab-panel",tabs:m,onSelect:t=>{r!==t&&l({status:t})},initialTabName:r??"all",children:d},r)}var jt=a(82974),bt=a(9003),Tt=a(84209),wt=a(77701);function vt({controls:t,title:e,onClick:a,variant:o}){const[i,n]=(0,s.useState)(!1),r=Object.values(t).filter((t=>t.slot)),l=i?"mailpoet-option-button-opener is-busy":"mailpoet-option-button-opener";return(0,E.jsxs)("div",{className:"mailpoet-option-button",children:[(0,E.jsx)(T.ZP,{isBusy:i,disabled:i,variant:o,className:"mailpoet-option-button-main",onClick:()=>{n(!0),a(n)},children:e}),r.length>0&&r.map((({key:t,slot:e})=>(0,E.jsx)(e,{},t))),Object.values(t).length>0&&(0,E.jsx)(ot.Z,{className:l,label:(0,x.__)("More","mailpoet"),icon:wt.Z,controls:Object.values(t).map((t=>({...t.control,onClick:()=>{n(!0),t.control.onClick()}}))),popoverProps:{placement:"bottom-start"}})]})}function Et(){const t=Tt.Hooks.applyFilters("mailpoet.automation.hero.actions",{});return(0,E.jsx)("section",{className:"mailpoet-automation-section mailpoet-automation-white-background",children:(0,E.jsxs)("div",{className:"mailpoet-automation-section-content mailpoet-automation-section-hero",children:[(0,E.jsxs)("div",{children:[(0,E.jsx)("span",{className:"mailpoet-automation-preheading",children:(0,x.__)("Automations","mailpoet")}),(0,E.jsx)("h1",{children:(0,x.__)("Better engagement begins with automation","mailpoet")}),(0,E.jsx)("p",{children:(0,x.__)("Send emails that inform, reward, and engage your audience through powerful segmenting, scheduling, and design tools.","mailpoet")}),(0,E.jsx)(vt,{variant:"primary",onClick:()=>{window.location.href=v.MailPoet.urls.automationTemplates},title:(0,x.__)("Start with a template","mailpoet"),controls:t})]}),(0,E.jsx)("img",{src:`${v.MailPoet.cdnUrl}automation/sections/hero.png`,alt:(0,x.__)("Welcome","mailpoet")})]})})}var Nt=a(38313),St=a(518);function kt(){const t=Nt.U.slice(0,3);return(0,E.jsx)("section",{className:"mailpoet-automation-section",children:(0,E.jsxs)("div",{className:"mailpoet-automation-section-content mailpoet-section-templates",children:[(0,E.jsx)("h2",{children:(0,x.__)("Explore essentials","mailpoet")}),(0,E.jsx)("p",{children:(0,x.__)("Choose from our list of pre-made templates and make it your own.","mailpoet")}),(0,E.jsx)("ul",{className:"mailpoet-section-template-list",children:(0,E.jsx)(St.p,{templates:t})}),(0,E.jsx)(T.ZP,{variant:"link",href:v.MailPoet.urls.automationTemplates,children:(0,x.__)("Browse all templates →","mailpoet")})]})})}function Pt(){const[t,e]=(0,s.useState)("start-with-a-trigger"),a=[{slug:"start-with-a-trigger",title:(0,x.__)("Start with a trigger","mailpoet"),text:(0,x.__)("Deliver relevant messages to your customers based on who they are and how they interact with your business.","mailpoet"),image:`${v.MailPoet.cdnUrl}automation/sections/start-with-a-trigger.png`},{slug:"customize-your-automation",title:(0,x.__)("Customize your automation","mailpoet"),text:(0,x.__)("Choose steps and create a custom journey to best suit your needs.","mailpoet"),image:`${v.MailPoet.cdnUrl}automation/sections/customize-your-automation.png`},{slug:"design-your-email",title:(0,x.__)("Design your email","mailpoet"),text:(0,x.__)("Modify one of our pre-made email templates or create your own design.","mailpoet"),image:`${v.MailPoet.cdnUrl}automation/sections/design-your-email.png`},{slug:"start-engaging",title:(0,x.__)("Start engaging","mailpoet"),text:(0,x.__)("Activate the automation and start engaging with your customers as they interact with your business.","mailpoet"),image:`${v.MailPoet.cdnUrl}automation/sections/start-engaging.png`}],o=a.filter((e=>e.slug===t))[0];return(0,E.jsx)("section",{className:"mailpoet-automation-section mailpoet-automation-white-background",children:(0,E.jsxs)("div",{className:"mailpoet-automation-section-content mailpoet-section-build-your-own",children:[(0,E.jsxs)("div",{children:[(0,E.jsx)("h2",{children:(0,x.__)("Build your own automations","mailpoet")}),(0,E.jsx)("p",{children:(0,x.__)("Create customized email sequences with our new automation editor.","mailpoet")}),(0,E.jsx)("ol",{children:a.map(((a,o)=>(0,E.jsxs)("li",{className:t===a.slug?"open":"",children:[(0,E.jsx)("div",{className:"marker",children:o<10?`0${o+1}`:o+1}),(0,E.jsxs)("div",{children:[(0,E.jsx)("button",{type:"button",onClick:()=>e(a.slug),className:"mailpoet-section-build-list-button",children:a.title}),(0,E.jsx)("p",{children:a.text})]})]},a.slug)))})]}),(0,E.jsx)("img",{src:o.image,alt:o.title})]})})}var Ot=a(55382),Ct=a(98097),Lt=a(82299);function Mt(){const t=(0,s.useCallback)((()=>{(0,I.E)({endpoint:"UserFlags",method:"set","data[legacy_automations_notice_dismissed]":"1"})}),[]);return(0,E.jsx)(Lt.q,{type:"info",timeout:!1,closable:!0,renderInPlace:!0,onClose:t,children:(0,E.jsx)("p",{children:(0,Ct.Z)((0,x.__)("Your existing automations are now listed here. You can also create new, more powerful automations with our new Automations editor. <link>Learn more</link>","mailpoet"),{link:(0,E.jsx)("a",{href:"https://kb.mailpoet.com/article/397-how-to-set-up-an-automation",target:"_blank",rel:"noopener noreferrer"})})})})}const Ut=()=>{v.MailPoet.trackEvent("Automations > Listing viewed")};function Dt(){const[t,e]=(0,s.useState)(!0),a=(0,c.Z)((t=>t(h).getAutomationCount()));(0,s.useEffect)((()=>{t&&0!==a&&(Ut(),e(!1))}),[t,a]);const o=a>0?(0,E.jsxs)(E.Fragment,{children:[(0,E.jsx)(At,{}),_.legacyAutomationCount>0&&!window.mailpoet_legacy_automations_notice_dismissed&&(0,E.jsx)(Mt,{}),(0,E.jsx)(xt,{})]}):(0,E.jsx)(Et,{});return(0,s.useEffect)((()=>{const t="mailpoet-automation-is-onboarding",e=document.querySelector("body");0!==a||e.classList.contains(t)||e.classList.add(t),a>0&&e.classList.contains(t)&&e.classList.remove(t)}),[a]),(0,E.jsxs)(E.Fragment,{children:[o,(0,E.jsx)(kt,{}),(0,E.jsx)(Pt,{})]})}function It(){return(0,Ot.a)(),(0,E.jsxs)(E.Fragment,{children:[(0,E.jsx)(u.Q,{}),(0,E.jsx)(bt.w,{}),(0,E.jsx)(Dt,{})]})}function Zt(){return(0,E.jsx)(m.zt,{children:(0,E.jsx)(l.BrowserRouter,{children:(0,E.jsx)(It,{})})})}window.addEventListener("DOMContentLoaded",(()=>{(()=>{const t=(0,g.Z)(h,{actions:{...o,...i},controls:f.controls,selectors:n,reducer:(t,e)=>function(t,e){switch(e.type){case"SET_LEGACY_AUTOMATIONS":return{...t,legacyAutomations:e.automations};case"UPDATE_LEGACY_AUTOMATION_STATUS":return{...t,legacyAutomations:t.legacyAutomations.map((t=>t.id===e.id?{...t,status:e.status}:t))};case"DELETE_LEGACY_AUTOMATION":return{...t,legacyAutomations:t.legacyAutomations.filter((t=>t.id!==e.id))};default:return t}}(function(t,e){switch(e.type){case"SET_AUTOMATIONS":return{...t,automations:e.automations};case"ADD_AUTOMATION":return{...t,automations:[e.automation,...t.automations]};case"UPDATE_AUTOMATION":return{...t,automations:t.automations.map((t=>t.id===e.automation.id?e.automation:t))};case"DELETE_AUTOMATION":return{...t,automations:t.automations.filter((t=>t.id!==e.automation.id))};default:return t}}(t,e),e),initialState:{automations:void 0,legacyAutomations:void 0}});(0,y.register)(t)})();const t=document.getElementById("mailpoet_automation");t&&((0,d.registerTranslations)(),(0,jt.y)(),(0,p.q)(),(0,r.s)(t).render((0,E.jsx)(Zt,{})))}))},65311:function(t){t.exports=jQuery}},function(t){t.O(0,[351],(function(){return 33916,t(t.s=33916)})),t.O()}]);