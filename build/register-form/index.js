/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/register-form/edit.js":
/*!***********************************!*\
  !*** ./src/register-form/edit.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);



function Edit({
  attributes,
  setAttributes
}) {
  const {
    candidate_username,
    candidate_email,
    candidate_pass,
    candidate_confirm_pass
  } = attributes;
  const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps)();
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Create Account', 'jobly')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Name', 'jobly'), "*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    value: candidate_username,
    onChange: e => setAttributes({
      candidate_username: e.target.value
    }),
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Rashed Kabir', 'jobly')
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Email', 'jobly'), "*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "email",
    value: candidate_email,
    onChange: e => setAttributes({
      candidate_email: e.target.value
    }),
    placeholder: "rshdkabir@gmail.com"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Password', 'jobly'), "*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "password",
    value: candidate_pass,
    onChange: e => setAttributes({
      candidate_pass: e.target.value
    }),
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Enter Password', 'jobly')
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Confirm Password', 'jobly'), "*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "password",
    value: candidate_confirm_pass,
    onChange: e => setAttributes({
      candidate_confirm_pass: e.target.value
    }),
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Confirm Password', 'jobly')
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "checkbox",
    id: "remember",
    required: true
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "remember"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('By hitting the "Register" button, you agree to the', 'jobly'), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Terms conditions', 'jobly')), " & ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Privacy Policy', 'jobly')))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "btn-eleven"
  }, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Register', 'jobly'))));
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Edit);

/***/ }),

/***/ "./src/register-form/save.js":
/*!***********************************!*\
  !*** ./src/register-form/save.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__);


function Save({
  attributes
}) {
  const blockProps = _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_1__.useBlockProps.save({
    className: 'registration-section position-relative pt-100 lg-pt-80 pb-150 lg-pb-80'
  });
  const {
    candidate_username,
    candidate_email,
    candidate_pass,
    candidate_confirm_pass
  } = attributes;
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    ...blockProps
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "container"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "user-data-form"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "text-center"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h2", null, "Create Account")), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "form-wrapper m-auto"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", {
    className: "nav nav-tabs border-0 w-100 mt-30",
    role: "tablist"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: "nav-item",
    role: "presentation"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "nav-link active",
    "data-bs-toggle": "tab",
    "data-bs-target": "#fc1",
    role: "tab"
  }, "Candidates")), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
    className: "nav-item",
    role: "presentation"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "nav-link",
    "data-bs-toggle": "tab",
    "data-bs-target": "#fc2",
    role: "tab"
  }, "Employer"))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "tab-content mt-40"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "tab-pane fade show active",
    role: "tabpanel",
    id: "fc1"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
    action: "#"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-25"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Name*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    placeholder: "Rashed Kabir"
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-25"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Email*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "email",
    placeholder: "rshdkabir@gmail.com"
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-20"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Password*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "password",
    placeholder: "Enter Password",
    className: "pass_log_id"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "placeholder_icon"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "passVicon"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "images/icon/icon_60.svg",
    alt: ""
  }))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "agreement-checkbox d-flex justify-content-between align-items-center"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "checkbox",
    id: "remember"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "remember"
  }, "By hitting the \"Register\" button, you agree to the ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, "Terms conditions"), " & ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, "Privacy Policy"))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "btn-eleven fw-500 tran3s d-block mt-20"
  }, "Register"))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "tab-pane fade",
    role: "tabpanel",
    id: "fc2"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("form", {
    action: "#"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-25"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Name*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    placeholder: "Zubayer Hasan"
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-25"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Email*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "email",
    placeholder: "zubayerhasan@gmail.com"
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "input-group-meta position-relative mb-20"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", null, "Password*"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "password",
    placeholder: "Enter Password",
    className: "pass_log_id"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "placeholder_icon"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "passVicon"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "images/icon/icon_60.svg",
    alt: ""
  }))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "agreement-checkbox d-flex justify-content-between align-items-center"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "checkbox",
    id: "remember"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    htmlFor: "remember"
  }, "By hitting the \"Register\" button, you agree to the ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, "Terms conditions"), " & ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#"
  }, "Privacy Policy"))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-12"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("button", {
    className: "btn-eleven fw-500 tran3s d-block mt-20"
  }, "Register")))))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "d-flex align-items-center mt-30 mb-10"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "line"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "pe-3 ps-3"
  }, "OR"), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "line"
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "row"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-sm-6"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#",
    className: "social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "images/icon/google.png",
    alt: ""
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "ps-2"
  }, "Signup with Google"))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "col-sm-6"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#",
    className: "social-use-btn d-flex align-items-center justify-content-center tran3s w-100 mt-10"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
    src: "images/icon/facebook.png",
    alt: ""
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "ps-2"
  }, "Signup with Facebook")))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    className: "text-center mt-10"
  }, "Have an account? ", (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    href: "#",
    className: "fw-500",
    "data-bs-toggle": "modal",
    "data-bs-target": "#loginModal"
  }, "Sign In"))))));
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Save);

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

module.exports = window["React"];

/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ ((module) => {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ ((module) => {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "./src/register-form/block.json":
/*!**************************************!*\
  !*** ./src/register-form/block.json ***!
  \**************************************/
/***/ ((module) => {

module.exports = JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"jobly/register-form","title":"Register Form (Jobly)","category":"jobly-blocks","icon":"dashicons dashicons-shortcode","description":"A custom registration form for candidates and employers.","supports":{"html":false,"anchor":true},"attributes":{"candidate_username":{"type":"string","default":"demo"},"candidate_email":{"type":"string","default":"demo@gmail.com"},"candidate_pass":{"type":"string","default":"demo"},"candidate_confirm_pass":{"type":"string","default":"demo"},"preview":{"type":"boolean","default":false}},"example":{"attributes":{"preview":true}},"keywords":["jobly","Candidate Register","Employer Register","Register Form"],"textdomain":"jobly","editorScript":"file:./index.js","editorStyle":"file:./editor.css"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./src/register-form/index.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./block.json */ "./src/register-form/block.json");
/* harmony import */ var _edit__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./edit */ "./src/register-form/edit.js");
/* harmony import */ var _save__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./save */ "./src/register-form/save.js");



/**
 * Internal dependencies
 */



/**
 * Block Registration
 */
(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_0__.registerBlockType)(_block_json__WEBPACK_IMPORTED_MODULE_1__.name, {
  edit: _edit__WEBPACK_IMPORTED_MODULE_2__["default"],
  save: _save__WEBPACK_IMPORTED_MODULE_3__["default"]
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map