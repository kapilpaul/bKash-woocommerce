(self.webpackChunkpayment_gateway_bkash_for_wc=self.webpackChunkpayment_gateway_bkash_for_wc||[]).push([[808],{8741:(e,t,a)=>{"use strict";var o;a.d(t,{b:()=>d}),e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var r,n,s="/dc-bkash/v1",d={v1:{settings:s+"/settings",transactions:s+"/transactions",transactionSearch:s+"/transactions/?search=",getToken:s+"/payment/get-token",createPayment:s+"/payment/create-payment",queryPayment:s+"/payment/query-payment/",executePayment:s+"/payment/execute-payment/",searchPayment:s+"/payment/search-payment/",docRefundPayment:s+"/payment/refund-payment/",refund:s+"/transactions/refund",upgrade:s+"/upgrade"}};(r="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(r.register(s,"v1","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/constants.js"),r.register(d,"API","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/constants.js")),(n="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&n(e)},16:(e,t,a)=>{"use strict";a.d(t,{Z:()=>c});var o,r=a(7294),n=a(1570),s=a(5732);function d(){return r.createElement(r.Fragment,null,r.createElement(s.Z,null))}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var l=(0,n.w)(d);const c=l;var i,u;(i="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(i.register(d,"App","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/App.js"),i.register(l,"default","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/App.js")),(u="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&u(e)},5732:(e,t,a)=>{"use strict";a.d(t,{Z:()=>m});var o,r=a(7294),n=a(4730),s=a(6890),d=a(7606),l=a.n(d),c=a(9249),i=a(8741);function u(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,o=new Array(t);a<t;a++)o[a]=e[a];return o}e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e);var p="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default.signature:function(e){return e};function b(){var e,t,a=(e=(0,r.useState)(!1),t=2,function(e){if(Array.isArray(e))return e}(e)||function(e,t){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e)){var a=[],o=!0,r=!1,n=void 0;try{for(var s,d=e[Symbol.iterator]();!(o=(s=d.next()).done)&&(a.push(s.value),!t||a.length!==t);o=!0);}catch(e){r=!0,n=e}finally{try{o||null==d.return||d.return()}finally{if(r)throw n}}return a}}(e,t)||function(e,t){if(e){if("string"==typeof e)return u(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(e):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?u(e,t):void 0}}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()),o=a[0],d=a[1];return r.createElement("div",{id:"dc-bkash-upgrade-notice"},r.createElement("div",{id:"dc-bkash-upgrade-notice-icon"},r.createElement("div",{id:"dc-bkash-upgrade-notice-message"},r.createElement("div",{id:"dc-bkash-upgrade-notice-title"},r.createElement("p",null,r.createElement("strong",null,(0,n.__)("bKash Data Update Required","dc-bkash")))),r.createElement("div",{id:"dc-bkash-upgrade-notice-content"},r.createElement("p",null,(0,n.__)("We need to update your install to the latest version","dc-bkash"))),r.createElement(s.Z,{type:"submit",className:"wc-update-now bg-bkash text-white",onClick:function(){return d(!0),void l()({path:i.b.v1.upgrade,method:"POST",data:{}}).then((function(e){d(!1),c.Am.success((0,n.__)("Updated Successfully!","dc-bkash")),document.querySelector(".dc-bkash-notice-info").classList.add("dc-bkash-notice-info-hide")})).catch((function(e){d(!1),c.Am.error(e.data.status+" : "+e.message)}))},isBusy:o,disabled:o},o?(0,n.__)("Updating","dc-bkash"):(0,n.__)("Update","dc-bkash")))))}c.Am.configure({position:"top-right",autoClose:5e3,closeOnClick:!1,pauseOnHover:!1,draggable:!1,closeButton:!1,style:{top:"3em"}}),p(b,"useState{[ isSubmitted, setIsSubmitted ](false)}");var f=b;const m=f;var y,h;(y="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&(y.register(b,"Upgrades","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/Pages/Upgrades.js"),y.register(f,"default","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/Pages/Upgrades.js")),(h="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&h(e)},3876:(e,t,a)=>{"use strict";var o,r=a(7294),n=a(9060),s=a(16);e=a.hmd(e),(o="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.enterModule:void 0)&&o(e),"undefined"!=typeof reactHotLoaderGlobal&&reactHotLoaderGlobal.default.signature;var d,l,c=document.getElementById("dc-bkash-upgrade-notice-container");n.render(r.createElement(s.Z,null),c),(d="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.default:void 0)&&d.register(c,"mountNode","/Users/kapilpaul/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/index.js"),(l="undefined"!=typeof reactHotLoaderGlobal?reactHotLoaderGlobal.leaveModule:void 0)&&l(e)},7606:e=>{"use strict";e.exports=wp.apiFetch}},0,[[3876,666,216]]]);