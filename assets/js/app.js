(self.webpackChunkpayment_gateway_bkash_for_wc=self.webpackChunkpayment_gateway_bkash_for_wc||[]).push([[143],{132:(e,t,a)=>{"use strict";a.d(t,{Z:()=>c});var o,r=a(294),n=a(570),s=a(983),l=a(667);function d(){return r.createElement(r.Fragment,null,r.createElement(s.Z,null),r.createElement("div",{className:"wrap"},r.createElement(l.Z,null)))}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var i=(0,n.w)(d);const c=i;var u,f;(u="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(u.register(d,"App","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/App.js"),u.register(i,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/App.js")),(f="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&f(e)},243:(e,t,a)=>{"use strict";a.d(t,{Z:()=>g});var o=a(294),r=a(730),n=a(151),s=a(459);const l=wp.apiFetch;var d,i=a.n(l),c=a(374);function u(e,t){var a=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),a.push.apply(a,o)}return a}function f(e){for(var t=1;t<arguments.length;t++){var a=null!=arguments[t]?arguments[t]:{};t%2?u(Object(a),!0).forEach((function(t){m(e,t,a[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(a)):u(Object(a)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(a,t))}))}return e}function m(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function p(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var a=[],o=!0,r=!1,n=void 0;try{for(var s,l=e[Symbol.iterator]();!(o=(s=l.next()).done)&&(a.push(s.value),!t||a.length!==t);o=!0);}catch(e){r=!0,n=e}finally{try{o||null==l.return||l.return()}finally{if(r)throw n}}return a}}(e,t)||function(e,t){if(e){if("string"==typeof e)return b(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(e):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?b(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function b(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,o=new Array(t);a<t;a++)o[a]=e[a];return o}function v(){var e=p((0,o.useState)({sections:{},fields:{gateway:{},dokan_integration:{}}}),2),t=e[0],a=e[1],l=p((0,o.useState)([]),2),d=l[0],u=l[1],b=p((0,o.useState)(!0),2),v=b[0],w=b[1],g=p((0,o.useState)(""),2),h=g[0],y=g[1],H=p((0,o.useState)(!1),2),L=H[0],G=H[1],k=function(e,o,r){var n,s,l;a(f(f({},t),{},{fields:f(f({},t.fields),{},m({},o,f(f({},null===(n=t.fields)||void 0===n?void 0:n[o]),{},m({},r,f(f({},null===(s=t.fields)||void 0===s||null===(l=s[o])||void 0===l?void 0:l[r]),{},{value:e})))))}))};return(0,o.useEffect)((function(){w(!0),i()({path:"/dc-bkash/v1/settings"}).then((function(e){w(!1),a(e),u(e.sections)})).catch((function(e){w(!1),console.log(e)}))}),[]),v?o.createElement("div",null,o.createElement(n.Z,null)," ",(0,r.__)("Loading...")):o.createElement("div",null,o.createElement("h2",null,(0,r.__)("Settings",window.dc_bkash_admin.text_domain)),o.createElement("div",{className:"dokan_admin_settings_area"},o.createElement("div",{className:"admin_settings_sections"},o.createElement("ul",{className:"dokan_admin_settings"},Object.keys(d).map((function(e){return o.createElement("li",m({className:"active",key:e,onClick:function(){return y(e)}},"className",h===e?"active":""),d[e].title)})))),o.createElement("div",{className:"admin_settings_fields"},Object.keys(t.fields).map((function(e,r){if(e===h)return o.createElement("div",{key:r,className:"single_settings_container"},o.createElement("p",{className:"section_title"},d[e].title),Object.keys(null==t?void 0:t.fields[e]).map((function(a,r){var n;return o.createElement("div",{key:r,className:"single_settings_field"},o.createElement(c.Z,{field:null==t?void 0:t.fields[e][a],section_id:e,id:a,handleChange:k,value:null==t||null===(n=t.fields[e][a])||void 0===n?void 0:n.value,allSettings:null==t?void 0:t.fields}))})),o.createElement(s.Z,{type:"submit",isBusy:L,disabled:L,className:"dc_bkash_save_btn",isPrimary:!0,onClick:function(){return G(!0),void i()({path:"/dc-bkash/v1/settings",method:"POST",data:t}).then((function(e){G(!1),a(e),u(e.sections)})).catch((function(e){G(!1),console.log(e)}))}},L?"Saving":"Save"))})))))}e=a.hmd(e),(d="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&d(e),("undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default.signature:function(e){return e})(v,"useState{[settings, setSettings]({\n    sections: {},\n    fields: {\n      gateway: {},\n      dokan_integration: {},\n    },\n  })}\nuseState{[sections, setSections]([])}\nuseState{[isFetching, setIsFetching](true)}\nuseState{[currentTab, setCurrentTab]('')}\nuseState{[isSubmitted, setIsSubmitted](false)}\nuseEffect{}");var w=v;const g=w;var h,y;(h="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(h.register(v,"Settings","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/Pages/settings.js"),h.register(w,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/Pages/settings.js")),(y="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&y(e)},983:(e,t,a)=>{"use strict";a.d(t,{Z:()=>d});var o,r=a(294);function n(){return r.createElement("div",{className:"bkash_header_container"},r.createElement("div",{className:"header_logo"},r.createElement("img",{src:s(),alt:""})))}function s(){return window.dc_bkash_admin.asset_url+"/images/bkash_logo.png"}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var l=n;const d=l;var i,c;(i="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(i.register(n,"Header","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/Header.js"),i.register(s,"getLogo","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/Header.js"),i.register(l,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/Header.js")),(c="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&c(e)},374:(e,t,a)=>{"use strict";a.d(t,{Z:()=>d});var o,r=a(294);function n(e){var t,a=e.field,o=e.id,n=e.handleChange,l=e.value,d=void 0===l?"":l,i=e.section_id,c=e.allSettings;return!(null!=a&&a.show_if&&!a.show_if.map((function(e){var a,o,r,n;return s(e,null!==(o=t=null==c||null===(a=c[i])||void 0===a?void 0:a[e.key])&&void 0!==o&&o.value?null===(r=t)||void 0===r?void 0:r.value:null===(n=t)||void 0===n?void 0:n.default)})).every((function(e){return!0===e})))&&(d=""===d?null==a?void 0:a.default:d,r.createElement(r.Fragment,null,r.createElement("p",{className:"label"},null==a?void 0:a.title),function(e){var t=e.type;switch(t){case"text":case"password":return r.createElement("input",{type:t,className:"widefat",value:d,onChange:function(e){return n(e.target.value,i,o)}});case"checkbox":return r.createElement(r.Fragment,null,r.createElement("input",{type:"checkbox",className:"widefat",id:o,value:d,onChange:function(e){return n(e.target.value,i,o)}}),r.createElement("label",{htmlFor:o},e.title));case"select":var a=Object.entries(e.options);return r.createElement(r.Fragment,null,r.createElement("select",{className:"widefat",value:d,onChange:function(e){return n(e.target.value,i,o)}},a.map((function(e,t){return r.createElement("option",{key:t,value:e[0]},e[1])}))));case"textarea":return r.createElement(r.Fragment,null,r.createElement("textarea",{id:o,cols:"30",rows:"10",className:"widefat"}));default:return""}}(a),a.description?r.createElement("p",{className:"help-text"},a.description):""))}function s(e,t){switch(null==e?void 0:e.condition){case"equal":if(t===e.value)return!0}return!1}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var l=n;const d=l;var i,c;(i="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(i.register(n,"Fields","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/fields.js"),i.register(s,"is_matched","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/fields.js"),i.register(l,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/components/fields.js")),(c="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&c(e)},181:(e,t,a)=>{"use strict";var o,r=a(294),n=a(60),s=a(132),l=a(17),d=a(727);e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var i,c,u=document.getElementById("hmr-app");n.render(r.createElement(d.VK,null,r.createElement(s.Z,null)),u),(0,l.Z)("dc-bkash"),(i="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&i.register(u,"mountNode","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/index.js"),(c="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&c(e)},667:(e,t,a)=>{"use strict";a.d(t,{Z:()=>m});var o,r=a(294),n=a(727),s=a(977),l=a(243);function d(){return(d=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var o in a)Object.prototype.hasOwnProperty.call(a,o)&&(e[o]=a[o])}return e}).apply(this,arguments)}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var i=[{path:"/settings",component:l.Z}];function c(){return r.createElement(r.Fragment,null,r.createElement(n.UT,null,r.createElement(s.rs,null,i.map((function(e,t){return r.createElement(u,d({key:t},e))})))))}function u(e){return r.createElement(s.AW,{path:e.path,component:e.component})}var f=c;const m=f;var p,b;(p="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(p.register(i,"routes","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/router/index.js"),p.register(c,"Routerview","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/router/index.js"),p.register(u,"RenderRoute","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/router/index.js"),p.register(f,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/router/index.js")),(b="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&b(e)},17:(e,t,a)=>{"use strict";var o;function r(e){var t=jQuery,a=t("#toplevel_page_"+e),o=window.location.href,r=o.substr(o.indexOf("admin.php"));a.on("click","a",(function(){var e=t(this);t("ul.wp-submenu li",a).removeClass("current"),e.hasClass("wp-has-submenu")?t("li.wp-first-item",a).addClass("current"):e.parents("li").addClass("current")})),t("ul.wp-submenu a",a).each((function(e,a){t(a).attr("href")!==r||t(a).parent().addClass("current")}))}a.d(t,{Z:()=>s}),e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var n=r;const s=n;var l,d;(l="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(l.register(r,"menuFix","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/utils/admin-menu-fix.js"),l.register(n,"default","/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/admin/utils/admin-menu-fix.js")),(d="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&d(e)}},0,[[181,666,216]]]);