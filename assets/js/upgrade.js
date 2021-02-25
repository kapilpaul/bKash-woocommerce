/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkpayment_gateway_bkash_for_wc"] = self["webpackChunkpayment_gateway_bkash_for_wc"] || []).push([["upgrade"],{

/***/ "./assets/src/upgrade/App.js":
/*!***********************************!*\
  !*** ./assets/src/upgrade/App.js ***!
  \***********************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => __WEBPACK_DEFAULT_EXPORT__\n/* harmony export */ });\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ \"./node_modules/react/index.js\");\n/* harmony import */ var react_hot_loader_root__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! react-hot-loader/root */ \"./node_modules/react-hot-loader/root.js\");\n/* harmony import */ var _Pages_upgrades__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Pages/upgrades */ \"./assets/src/upgrade/Pages/upgrades.js\");\n/* module decorator */ module = __webpack_require__.hmd(module);\n(function () {\n  var enterModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.enterModule : undefined;\n  enterModule && enterModule(module);\n})();\n\nvar __signature__ = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal[\"default\"].signature : function (a) {\n  return a;\n};\n\n\n\n\n\nfunction App() {\n  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_Pages_upgrades__WEBPACK_IMPORTED_MODULE_1__.default, null));\n}\n\nvar _default = (0,react_hot_loader_root__WEBPACK_IMPORTED_MODULE_2__.hot)(App);\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_default);\n;\n\n(function () {\n  var reactHotLoader = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.default : undefined;\n\n  if (!reactHotLoader) {\n    return;\n  }\n\n  reactHotLoader.register(App, \"App\", \"/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/App.js\");\n  reactHotLoader.register(_default, \"default\", \"/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/App.js\");\n})();\n\n;\n\n(function () {\n  var leaveModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.leaveModule : undefined;\n  leaveModule && leaveModule(module);\n})();\n\n//# sourceURL=webpack://payment-gateway-bkash-for-wc/./assets/src/upgrade/App.js?");

/***/ }),

/***/ "./assets/src/upgrade/Pages/upgrades.js":
/*!**********************************************!*\
  !*** ./assets/src/upgrade/Pages/upgrades.js ***!
  \**********************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => __WEBPACK_DEFAULT_EXPORT__\n/* harmony export */ });\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ \"./node_modules/react/index.js\");\n/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/i18n */ \"./node_modules/@wordpress/i18n/build-module/index.js\");\n/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/components */ \"./node_modules/@wordpress/components/build-module/button/index.js\");\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/api-fetch */ \"@wordpress/api-fetch\");\n/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__);\n/* harmony import */ var react_toastify__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! react-toastify */ \"./node_modules/react-toastify/dist/react-toastify.esm.js\");\n/* harmony import */ var _admin_styles_react_toastify_css__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../../admin/styles/react-toastify.css */ \"./assets/src/admin/styles/react-toastify.css\");\n/* module decorator */ module = __webpack_require__.hmd(module);\n(function () {\n  var enterModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.enterModule : undefined;\n  enterModule && enterModule(module);\n})();\n\nfunction _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }\n\nfunction _nonIterableRest() { throw new TypeError(\"Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.\"); }\n\nfunction _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === \"string\") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === \"Object\" && o.constructor) n = o.constructor.name; if (n === \"Map\" || n === \"Set\") return Array.from(o); if (n === \"Arguments\" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }\n\nfunction _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }\n\nfunction _iterableToArrayLimit(arr, i) { if (typeof Symbol === \"undefined\" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i[\"return\"] != null) _i[\"return\"](); } finally { if (_d) throw _e; } } return _arr; }\n\nfunction _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }\n\nvar __signature__ = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal[\"default\"].signature : function (a) {\n  return a;\n};\n\n\n\n\n\n\n\n/**\n * configure the toast\n */\n\nreact_toastify__WEBPACK_IMPORTED_MODULE_4__.toast.configure({\n  position: 'top-right',\n  autoClose: 5000,\n  closeOnClick: false,\n  pauseOnHover: false,\n  draggable: false,\n  closeButton: false,\n  style: {\n    top: '3em'\n  }\n});\n\nfunction Upgrades() {\n  var _useState = (0,react__WEBPACK_IMPORTED_MODULE_0__.useState)(false),\n      _useState2 = _slicedToArray(_useState, 2),\n      isSubmitted = _useState2[0],\n      setIsSubmitted = _useState2[1];\n  /**\n   * Handle update from here.\n   */\n\n\n  var handleUpdate = function handleUpdate() {\n    setIsSubmitted(true);\n    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({\n      path: '/dc-bkash/v1/upgrade',\n      method: 'POST',\n      data: {}\n    }).then(function (resp) {\n      setIsSubmitted(false); // toast.success(__('Saved Successfully!', dc_bkash_admin.text_domain));\n    })[\"catch\"](function (err) {\n      setIsSubmitted(false);\n      react_toastify__WEBPACK_IMPORTED_MODULE_4__.toast.error(err.data.status + ' : ' + err.message);\n    });\n  };\n\n  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"div\", {\n    id: \"dc-bkash-upgrade-notice\"\n  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"div\", {\n    id: \"dc-bkash-upgrade-notice-icon\"\n  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"div\", {\n    id: \"dc-bkash-upgrade-notice-message\"\n  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"div\", {\n    id: \"dc-bkash-upgrade-notice-title\"\n  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"p\", null, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"strong\", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('bKash Data Update Required', dc_bkash_admin.text_domain)))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"div\", {\n    id: \"dc-bkash-upgrade-notice-content\"\n  }, /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(\"p\", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('We need to update your install to the latest version', dc_bkash_admin.text_domain))), /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_wordpress_components__WEBPACK_IMPORTED_MODULE_5__.default, {\n    type: \"submit\",\n    className: \"wc-update-now bg-bkash text-white\",\n    onClick: function onClick() {\n      return handleUpdate();\n    },\n    isBusy: isSubmitted,\n    disabled: isSubmitted\n  }, isSubmitted ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Updating', dc_bkash_admin.text_domain) : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_1__.__)('Update', dc_bkash_admin.text_domain)))));\n}\n\n__signature__(Upgrades, \"useState{[isSubmitted, setIsSubmitted](false)}\");\n\nvar _default = Upgrades;\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_default);\n;\n\n(function () {\n  var reactHotLoader = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.default : undefined;\n\n  if (!reactHotLoader) {\n    return;\n  }\n\n  reactHotLoader.register(Upgrades, \"Upgrades\", \"/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/Pages/upgrades.js\");\n  reactHotLoader.register(_default, \"default\", \"/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/Pages/upgrades.js\");\n})();\n\n;\n\n(function () {\n  var leaveModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.leaveModule : undefined;\n  leaveModule && leaveModule(module);\n})();\n\n//# sourceURL=webpack://payment-gateway-bkash-for-wc/./assets/src/upgrade/Pages/upgrades.js?");

/***/ }),

/***/ "./assets/src/upgrade/index.js":
/*!*************************************!*\
  !*** ./assets/src/upgrade/index.js ***!
  \*************************************/
/***/ ((module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ \"./node_modules/react/index.js\");\n/* harmony import */ var react_dom__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react-dom */ \"./node_modules/@hot-loader/react-dom/index.js\");\n/* harmony import */ var _App__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./App */ \"./assets/src/upgrade/App.js\");\n/* harmony import */ var _styles_style_scss__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./styles/style.scss */ \"./assets/src/upgrade/styles/style.scss\");\n/* module decorator */ module = __webpack_require__.hmd(module);\n(function () {\n  var enterModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.enterModule : undefined;\n  enterModule && enterModule(module);\n})();\n\nvar __signature__ = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal[\"default\"].signature : function (a) {\n  return a;\n};\n\n\n\n\n\n\nvar mountNode = document.getElementById('dc-bkash-upgrade-notice-container');\nreact_dom__WEBPACK_IMPORTED_MODULE_1__.render( /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement(_App__WEBPACK_IMPORTED_MODULE_2__.default, null), mountNode);\n;\n\n(function () {\n  var reactHotLoader = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.default : undefined;\n\n  if (!reactHotLoader) {\n    return;\n  }\n\n  reactHotLoader.register(mountNode, \"mountNode\", \"/Users/wedevs/Codes/wordpress/woobkash/wp-content/plugins/bKash-woocommerce/assets/src/upgrade/index.js\");\n})();\n\n;\n\n(function () {\n  var leaveModule = typeof reactHotLoaderGlobal !== 'undefined' ? reactHotLoaderGlobal.leaveModule : undefined;\n  leaveModule && leaveModule(module);\n})();\n\n//# sourceURL=webpack://payment-gateway-bkash-for-wc/./assets/src/upgrade/index.js?");

/***/ }),

/***/ "./assets/src/admin/styles/react-toastify.css":
/*!****************************************************!*\
  !*** ./assets/src/admin/styles/react-toastify.css ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://payment-gateway-bkash-for-wc/./assets/src/admin/styles/react-toastify.css?");

/***/ }),

/***/ "./assets/src/upgrade/styles/style.scss":
/*!**********************************************!*\
  !*** ./assets/src/upgrade/styles/style.scss ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n// extracted by mini-css-extract-plugin\n\n\n//# sourceURL=webpack://payment-gateway-bkash-for-wc/./assets/src/upgrade/styles/style.scss?");

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/***/ ((module) => {

"use strict";
module.exports = wp.apiFetch;

/***/ })

},
0,[["./assets/src/upgrade/index.js","runtime","vendors"]]]);