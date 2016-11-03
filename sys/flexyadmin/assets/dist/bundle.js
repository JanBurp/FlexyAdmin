/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.l = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// identity function for calling harmory imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };

/******/ 	// define getter function for harmory exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		Object.defineProperty(exports, name, {
/******/ 			configurable: false,
/******/ 			enumerable: true,
/******/ 			get: getter
/******/ 		});
/******/ 	};

/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};

/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/flexyadmin/assets/dist/";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 129);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(module) {'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};/*!
 * @name JavaScript/NodeJS Merge v1.2.0
 * @author yeikos
 * @repository https://github.com/yeikos/js.merge

 * Copyright 2014 yeikos - MIT license
 * https://raw.github.com/yeikos/js.merge/master/LICENSE
 */;(function(isNode){/**
	 * Merge one or more objects 
	 * @param bool? clone
	 * @param mixed,... arguments
	 * @return object
	 */var Public=function Public(clone){return merge(clone===true,false,arguments);},publicName='merge';/**
	 * Merge two or more objects recursively 
	 * @param bool? clone
	 * @param mixed,... arguments
	 * @return object
	 */Public.recursive=function(clone){return merge(clone===true,true,arguments);};/**
	 * Clone the input removing any reference
	 * @param mixed input
	 * @return mixed
	 */Public.clone=function(input){var output=input,type=typeOf(input),index,size;if(type==='array'){output=[];size=input.length;for(index=0;index<size;++index){output[index]=Public.clone(input[index]);}}else if(type==='object'){output={};for(index in input){output[index]=Public.clone(input[index]);}}return output;};/**
	 * Merge two objects recursively
	 * @param mixed input
	 * @param mixed extend
	 * @return mixed
	 */function merge_recursive(base,extend){if(typeOf(base)!=='object')return extend;for(var key in extend){if(typeOf(base[key])==='object'&&typeOf(extend[key])==='object'){base[key]=merge_recursive(base[key],extend[key]);}else{base[key]=extend[key];}}return base;}/**
	 * Merge two or more objects
	 * @param bool clone
	 * @param bool recursive
	 * @param array argv
	 * @return object
	 */function merge(clone,recursive,argv){var result=argv[0],size=argv.length;if(clone||typeOf(result)!=='object')result={};for(var index=0;index<size;++index){var item=argv[index],type=typeOf(item);if(type!=='object')continue;for(var key in item){var sitem=clone?Public.clone(item[key]):item[key];if(recursive){result[key]=merge_recursive(result[key],sitem);}else{result[key]=sitem;}}}return result;}/**
	 * Get type of variable
	 * @param mixed input
	 * @return string
	 *
	 * @see http://jsperf.com/typeofvar
	 */function typeOf(input){return{}.toString.call(input).slice(8,-1).toLowerCase();}if(isNode){module.exports=Public;}else{window[publicName]=Public;}})(( false?'undefined':_typeof(module))==='object'&&module&&_typeof(module.exports)==='object'&&module.exports);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(9)(module)))

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _field=__webpack_require__(87);var _field2=_interopRequireDefault(_field);var _watch=__webpack_require__(58);var _watch2=_interopRequireDefault(_watch);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}var props=__webpack_require__(57);var data=__webpack_require__(54);var methods=__webpack_require__(55);var computed=__webpack_require__(53);var mounted=__webpack_require__(56);module.exports=function(){return{render:_field2.default,mixins:[props,data,methods,computed,mounted,_watch2.default],methods:{setValue:function setValue(value){this.curValue=value;this.dirty=true;},updateValue:function updateValue(e){this.curValue=e.target.value;},getValue:function getValue(){return this.curValue;},reset:function reset(){this.wasReset=true;this.curValue='';},focus:function focus(){this.$el.getElementsByTagName(this.tagName)[0].focus();}}};};

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Buffer, module) {'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};var clone=function(){'use strict';/**
 * Clones (copies) an Object using deep copying.
 *
 * This function supports circular references by default, but if you are certain
 * there are no circular references in your object, you can save some CPU time
 * by calling clone(obj, false).
 *
 * Caution: if `circular` is false and `parent` contains circular references,
 * your program may enter an infinite loop and crash.
 *
 * @param `parent` - the object to be cloned
 * @param `circular` - set to true if the object to be cloned may contain
 *    circular references. (optional - true by default)
 * @param `depth` - set to a number if the object is only to be cloned to
 *    a particular depth. (optional - defaults to Infinity)
 * @param `prototype` - sets the prototype to be used when cloning an object.
 *    (optional - defaults to parent prototype).
*/function clone(parent,circular,depth,prototype){var filter;if((typeof circular==='undefined'?'undefined':_typeof(circular))==='object'){depth=circular.depth;prototype=circular.prototype;filter=circular.filter;circular=circular.circular;}// maintain two arrays for circular references, where corresponding parents
// and children have the same index
var allParents=[];var allChildren=[];var useBuffer=typeof Buffer!='undefined';if(typeof circular=='undefined')circular=true;if(typeof depth=='undefined')depth=Infinity;// recurse this function so we don't reset allParents and allChildren
function _clone(parent,depth){// cloning null always returns null
if(parent===null)return null;if(depth==0)return parent;var child;var proto;if((typeof parent==='undefined'?'undefined':_typeof(parent))!='object'){return parent;}if(clone.__isArray(parent)){child=[];}else if(clone.__isRegExp(parent)){child=new RegExp(parent.source,__getRegExpFlags(parent));if(parent.lastIndex)child.lastIndex=parent.lastIndex;}else if(clone.__isDate(parent)){child=new Date(parent.getTime());}else if(useBuffer&&Buffer.isBuffer(parent)){child=new Buffer(parent.length);parent.copy(child);return child;}else{if(typeof prototype=='undefined'){proto=Object.getPrototypeOf(parent);child=Object.create(proto);}else{child=Object.create(prototype);proto=prototype;}}if(circular){var index=allParents.indexOf(parent);if(index!=-1){return allChildren[index];}allParents.push(parent);allChildren.push(child);}for(var i in parent){var attrs;if(proto){attrs=Object.getOwnPropertyDescriptor(proto,i);}if(attrs&&attrs.set==null){continue;}child[i]=_clone(parent[i],depth-1);}return child;}return _clone(parent,depth);}/**
 * Simple flat clone using prototype, accepts only objects, usefull for property
 * override on FLAT configuration object (no nested props).
 *
 * USE WITH CAUTION! This may not behave as you wish if you do not know how this
 * works.
 */clone.clonePrototype=function clonePrototype(parent){if(parent===null)return null;var c=function c(){};c.prototype=parent;return new c();};// private utility functions
function __objToStr(o){return Object.prototype.toString.call(o);};clone.__objToStr=__objToStr;function __isDate(o){return(typeof o==='undefined'?'undefined':_typeof(o))==='object'&&__objToStr(o)==='[object Date]';};clone.__isDate=__isDate;function __isArray(o){return(typeof o==='undefined'?'undefined':_typeof(o))==='object'&&__objToStr(o)==='[object Array]';};clone.__isArray=__isArray;function __isRegExp(o){return(typeof o==='undefined'?'undefined':_typeof(o))==='object'&&__objToStr(o)==='[object RegExp]';};clone.__isRegExp=__isRegExp;function __getRegExpFlags(re){var flags='';if(re.global)flags+='g';if(re.ignoreCase)flags+='i';if(re.multiline)flags+='m';return flags;};clone.__getRegExpFlags=__getRegExpFlags;return clone;}();if(( false?'undefined':_typeof(module))==='object'&&module.exports){module.exports=clone;}
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(12).Buffer, __webpack_require__(9)(module)))

/***/ },
/* 3 */
/***/ function(module, exports) {

"use strict";
"use strict";/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/// css base code, injected by the css-loader
module.exports=function(){var list=[];// return the list of modules as css string
list.toString=function toString(){var result=[];for(var i=0;i<this.length;i++){var item=this[i];if(item[2]){result.push("@media "+item[2]+"{"+item[1]+"}");}else{result.push(item[1]);}}return result.join("");};// import a list of modules into the list
list.i=function(modules,mediaQuery){if(typeof modules==="string")modules=[[null,modules,""]];var alreadyImportedModules={};for(var i=0;i<this.length;i++){var id=this[i][0];if(typeof id==="number")alreadyImportedModules[id]=true;}for(i=0;i<modules.length;i++){var item=modules[i];// skip already imported module
// this implementation is not 100% perfect for weird media query combinations
//  when a module is imported multiple times with different media queries.
//  I hope this will never occur (Hey this way we have smaller bundles)
if(typeof item[0]!=="number"||!alreadyImportedModules[item[0]]){if(mediaQuery&&!item[2]){item[2]=mediaQuery;}else if(mediaQuery){item[2]="("+item[2]+") and ("+mediaQuery+")";}list.push(item);}}};return list;};

/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _vue=__webpack_require__(10);var _vue2=_interopRequireDefault(_vue);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}exports.default=new _vue2.default();

/***/ },
/* 5 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _updateValue=__webpack_require__(14);var _updateValue2=_interopRequireDefault(_updateValue);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}var merge=__webpack_require__(0);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{props:{placeholder:{type:String,required:false,default:''},debounce:{type:Number,default:300},lazy:{type:Boolean}},data:function data(){return{lastKeyStroke:new Date()};},methods:{updateValue:_updateValue2.default}});};

/***/ },
/* 6 */
/***/ function(module, exports) {

/*
	MIT License http://www.opensource.org/licenses/mit-license.php
	Author Tobias Koppers @sokra
*/
var stylesInDom = {},
	memoize = function(fn) {
		var memo;
		return function () {
			if (typeof memo === "undefined") memo = fn.apply(this, arguments);
			return memo;
		};
	},
	isOldIE = memoize(function() {
		return /msie [6-9]\b/.test(window.navigator.userAgent.toLowerCase());
	}),
	getHeadElement = memoize(function () {
		return document.head || document.getElementsByTagName("head")[0];
	}),
	singletonElement = null,
	singletonCounter = 0,
	styleElementsInsertedAtTop = [];

module.exports = function(list, options) {
	if(typeof DEBUG !== "undefined" && DEBUG) {
		if(typeof document !== "object") throw new Error("The style-loader cannot be used in a non-browser environment");
	}

	options = options || {};
	// Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
	// tags it will allow on a page
	if (typeof options.singleton === "undefined") options.singleton = isOldIE();

	// By default, add <style> tags to the bottom of <head>.
	if (typeof options.insertAt === "undefined") options.insertAt = "bottom";

	var styles = listToStyles(list);
	addStylesToDom(styles, options);

	return function update(newList) {
		var mayRemove = [];
		for(var i = 0; i < styles.length; i++) {
			var item = styles[i];
			var domStyle = stylesInDom[item.id];
			domStyle.refs--;
			mayRemove.push(domStyle);
		}
		if(newList) {
			var newStyles = listToStyles(newList);
			addStylesToDom(newStyles, options);
		}
		for(var i = 0; i < mayRemove.length; i++) {
			var domStyle = mayRemove[i];
			if(domStyle.refs === 0) {
				for(var j = 0; j < domStyle.parts.length; j++)
					domStyle.parts[j]();
				delete stylesInDom[domStyle.id];
			}
		}
	};
}

function addStylesToDom(styles, options) {
	for(var i = 0; i < styles.length; i++) {
		var item = styles[i];
		var domStyle = stylesInDom[item.id];
		if(domStyle) {
			domStyle.refs++;
			for(var j = 0; j < domStyle.parts.length; j++) {
				domStyle.parts[j](item.parts[j]);
			}
			for(; j < item.parts.length; j++) {
				domStyle.parts.push(addStyle(item.parts[j], options));
			}
		} else {
			var parts = [];
			for(var j = 0; j < item.parts.length; j++) {
				parts.push(addStyle(item.parts[j], options));
			}
			stylesInDom[item.id] = {id: item.id, refs: 1, parts: parts};
		}
	}
}

function listToStyles(list) {
	var styles = [];
	var newStyles = {};
	for(var i = 0; i < list.length; i++) {
		var item = list[i];
		var id = item[0];
		var css = item[1];
		var media = item[2];
		var sourceMap = item[3];
		var part = {css: css, media: media, sourceMap: sourceMap};
		if(!newStyles[id])
			styles.push(newStyles[id] = {id: id, parts: [part]});
		else
			newStyles[id].parts.push(part);
	}
	return styles;
}

function insertStyleElement(options, styleElement) {
	var head = getHeadElement();
	var lastStyleElementInsertedAtTop = styleElementsInsertedAtTop[styleElementsInsertedAtTop.length - 1];
	if (options.insertAt === "top") {
		if(!lastStyleElementInsertedAtTop) {
			head.insertBefore(styleElement, head.firstChild);
		} else if(lastStyleElementInsertedAtTop.nextSibling) {
			head.insertBefore(styleElement, lastStyleElementInsertedAtTop.nextSibling);
		} else {
			head.appendChild(styleElement);
		}
		styleElementsInsertedAtTop.push(styleElement);
	} else if (options.insertAt === "bottom") {
		head.appendChild(styleElement);
	} else {
		throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
	}
}

function removeStyleElement(styleElement) {
	styleElement.parentNode.removeChild(styleElement);
	var idx = styleElementsInsertedAtTop.indexOf(styleElement);
	if(idx >= 0) {
		styleElementsInsertedAtTop.splice(idx, 1);
	}
}

function createStyleElement(options) {
	var styleElement = document.createElement("style");
	styleElement.type = "text/css";
	insertStyleElement(options, styleElement);
	return styleElement;
}

function addStyle(obj, options) {
	var styleElement, update, remove;

	if (options.singleton) {
		var styleIndex = singletonCounter++;
		styleElement = singletonElement || (singletonElement = createStyleElement(options));
		update = applyToSingletonTag.bind(null, styleElement, styleIndex, false);
		remove = applyToSingletonTag.bind(null, styleElement, styleIndex, true);
	} else {
		styleElement = createStyleElement(options);
		update = applyToTag.bind(null, styleElement);
		remove = function() {
			removeStyleElement(styleElement);
		};
	}

	update(obj);

	return function updateStyle(newObj) {
		if(newObj) {
			if(newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap)
				return;
			update(obj = newObj);
		} else {
			remove();
		}
	};
}

var replaceText = (function () {
	var textStore = [];

	return function (index, replacement) {
		textStore[index] = replacement;
		return textStore.filter(Boolean).join('\n');
	};
})();

function applyToSingletonTag(styleElement, index, remove, obj) {
	var css = remove ? "" : obj.css;

	if (styleElement.styleSheet) {
		styleElement.styleSheet.cssText = replaceText(index, css);
	} else {
		var cssNode = document.createTextNode(css);
		var childNodes = styleElement.childNodes;
		if (childNodes[index]) styleElement.removeChild(childNodes[index]);
		if (childNodes.length) {
			styleElement.insertBefore(cssNode, childNodes[index]);
		} else {
			styleElement.appendChild(cssNode);
		}
	}
}

function applyToTag(styleElement, obj) {
	var css = obj.css;
	var media = obj.media;
	var sourceMap = obj.sourceMap;

	if (media) {
		styleElement.setAttribute("media", media);
	}

	if (sourceMap) {
		// https://developer.chrome.com/devtools/docs/javascript-debugging
		// this makes source maps inside style tags work properly in Chrome
		css += '\n/*# sourceURL=' + sourceMap.sources[0] + ' */';
		// http://stackoverflow.com/a/26603875
		css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}

	if (styleElement.styleSheet) {
		styleElement.styleSheet.cssText = css;
	} else {
		while(styleElement.firstChild) {
			styleElement.removeChild(styleElement.firstChild);
		}
		styleElement.appendChild(document.createTextNode(css));
	}
}


/***/ },
/* 7 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return getForm(this.$parent);};function getForm(instance){if(!instance||!instance.hasOwnProperty('$parent'))return false;if(instance.hasOwnProperty('isForm')&&instance.isForm)return instance;return getForm(instance.$parent);}

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var isNumeric=__webpack_require__(15);module.exports=function(that){return!!(that.curValue&&(isString(that.curValue)||that.fieldType=='checkbox'||that.fieldType=='date'||isMultipleList(that)||isNumeric(that.curValue)));};function isMultipleList(that){return(that.fieldType=='select'||that.fieldType=='buttons')&&that.multiple&&that.curValue.length>0;}function isString(value){return typeof value=='string'&&value.trim().length>0;}

/***/ },
/* 9 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(module){if(!module.webpackPolyfill){module.deprecate=function(){};module.paths=[];// module.parent = undefined by default
if(!module.children)module.children=[];Object.defineProperty(module,"loaded",{enumerable:true,configurable:false,get:function get(){return module.l;}});Object.defineProperty(module,"id",{enumerable:true,configurable:false,get:function get(){return module.i;}});module.webpackPolyfill=1;}return module;};

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};/*!
 * Vue.js v2.0.3
 * (c) 2014-2016 Evan You
 * Released under the MIT License.
 */(function(global,factory){( false?'undefined':_typeof(exports))==='object'&&typeof module!=='undefined'?module.exports=factory(): true?!(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__)):global.Vue=factory();})(undefined,function(){'use strict';/*  *//**
 * Convert a value to a string that is actually rendered.
 */function _toString(val){return val==null?'':(typeof val==='undefined'?'undefined':_typeof(val))==='object'?JSON.stringify(val,null,2):String(val);}/**
 * Convert a input value to a number for persistence.
 * If the conversion fails, return original string.
 */function toNumber(val){var n=parseFloat(val,10);return n||n===0?n:val;}/**
 * Make a map and return a function for checking if a key
 * is in that map.
 */function makeMap(str,expectsLowerCase){var map=Object.create(null);var list=str.split(',');for(var i=0;i<list.length;i++){map[list[i]]=true;}return expectsLowerCase?function(val){return map[val.toLowerCase()];}:function(val){return map[val];};}/**
 * Check if a tag is a built-in tag.
 */var isBuiltInTag=makeMap('slot,component',true);/**
 * Remove an item from an array
 */function remove$1(arr,item){if(arr.length){var index=arr.indexOf(item);if(index>-1){return arr.splice(index,1);}}}/**
 * Check whether the object has the property.
 */var hasOwnProperty=Object.prototype.hasOwnProperty;function hasOwn(obj,key){return hasOwnProperty.call(obj,key);}/**
 * Check if value is primitive
 */function isPrimitive(value){return typeof value==='string'||typeof value==='number';}/**
 * Create a cached version of a pure function.
 */function cached(fn){var cache=Object.create(null);return function cachedFn(str){var hit=cache[str];return hit||(cache[str]=fn(str));};}/**
 * Camelize a hyphen-delmited string.
 */var camelizeRE=/-(\w)/g;var camelize=cached(function(str){return str.replace(camelizeRE,function(_,c){return c?c.toUpperCase():'';});});/**
 * Capitalize a string.
 */var capitalize=cached(function(str){return str.charAt(0).toUpperCase()+str.slice(1);});/**
 * Hyphenate a camelCase string.
 */var hyphenateRE=/([^-])([A-Z])/g;var hyphenate=cached(function(str){return str.replace(hyphenateRE,'$1-$2').replace(hyphenateRE,'$1-$2').toLowerCase();});/**
 * Simple bind, faster than native
 */function bind$1(fn,ctx){function boundFn(a){var l=arguments.length;return l?l>1?fn.apply(ctx,arguments):fn.call(ctx,a):fn.call(ctx);}// record original fn length
boundFn._length=fn.length;return boundFn;}/**
 * Convert an Array-like object to a real Array.
 */function toArray(list,start){start=start||0;var i=list.length-start;var ret=new Array(i);while(i--){ret[i]=list[i+start];}return ret;}/**
 * Mix properties into target object.
 */function extend(to,_from){for(var key in _from){to[key]=_from[key];}return to;}/**
 * Quick object check - this is primarily used to tell
 * Objects from primitive values when we know the value
 * is a JSON-compliant type.
 */function isObject(obj){return obj!==null&&(typeof obj==='undefined'?'undefined':_typeof(obj))==='object';}/**
 * Strict object type check. Only returns true
 * for plain JavaScript objects.
 */var toString=Object.prototype.toString;var OBJECT_STRING='[object Object]';function isPlainObject(obj){return toString.call(obj)===OBJECT_STRING;}/**
 * Merge an Array of Objects into a single Object.
 */function toObject(arr){var res={};for(var i=0;i<arr.length;i++){if(arr[i]){extend(res,arr[i]);}}return res;}/**
 * Perform no operation.
 */function noop(){}/**
 * Always return false.
 */var no=function no(){return false;};/**
 * Generate a static keys string from compiler modules.
 */function genStaticKeys(modules){return modules.reduce(function(keys,m){return keys.concat(m.staticKeys||[]);},[]).join(',');}/**
 * Check if two values are loosely equal - that is,
 * if they are plain objects, do they have the same shape?
 */function looseEqual(a,b){/* eslint-disable eqeqeq */return a==b||(isObject(a)&&isObject(b)?JSON.stringify(a)===JSON.stringify(b):false);/* eslint-enable eqeqeq */}function looseIndexOf(arr,val){for(var i=0;i<arr.length;i++){if(looseEqual(arr[i],val)){return i;}}return-1;}/*  */var config={/**
   * Option merge strategies (used in core/util/options)
   */optionMergeStrategies:Object.create(null),/**
   * Whether to suppress warnings.
   */silent:false,/**
   * Whether to enable devtools
   */devtools:"development"!=='production',/**
   * Error handler for watcher errors
   */errorHandler:null,/**
   * Ignore certain custom elements
   */ignoredElements:null,/**
   * Custom user key aliases for v-on
   */keyCodes:Object.create(null),/**
   * Check if a tag is reserved so that it cannot be registered as a
   * component. This is platform-dependent and may be overwritten.
   */isReservedTag:no,/**
   * Check if a tag is an unknown element.
   * Platform-dependent.
   */isUnknownElement:no,/**
   * Get the namespace of an element
   */getTagNamespace:noop,/**
   * Check if an attribute must be bound using property, e.g. value
   * Platform-dependent.
   */mustUseProp:no,/**
   * List of asset types that a component can own.
   */_assetTypes:['component','directive','filter'],/**
   * List of lifecycle hooks.
   */_lifecycleHooks:['beforeCreate','created','beforeMount','mounted','beforeUpdate','updated','beforeDestroy','destroyed','activated','deactivated'],/**
   * Max circular updates allowed in a scheduler flush cycle.
   */_maxUpdateCount:100,/**
   * Server rendering?
   */_isServer:"client"==='server'};/*  *//**
 * Check if a string starts with $ or _
 */function isReserved(str){var c=(str+'').charCodeAt(0);return c===0x24||c===0x5F;}/**
 * Define a property.
 */function def(obj,key,val,enumerable){Object.defineProperty(obj,key,{value:val,enumerable:!!enumerable,writable:true,configurable:true});}/**
 * Parse simple path.
 */var bailRE=/[^\w\.\$]/;function parsePath(path){if(bailRE.test(path)){return;}else{var segments=path.split('.');return function(obj){for(var i=0;i<segments.length;i++){if(!obj){return;}obj=obj[segments[i]];}return obj;};}}/*  *//* globals MutationObserver */// can we use __proto__?
var hasProto='__proto__'in{};// Browser environment sniffing
var inBrowser=typeof window!=='undefined'&&Object.prototype.toString.call(window)!=='[object Object]';var UA=inBrowser&&window.navigator.userAgent.toLowerCase();var isIE=UA&&/msie|trident/.test(UA);var isIE9=UA&&UA.indexOf('msie 9.0')>0;var isEdge=UA&&UA.indexOf('edge/')>0;var isAndroid=UA&&UA.indexOf('android')>0;var isIOS=UA&&/iphone|ipad|ipod|ios/.test(UA);// detect devtools
var devtools=inBrowser&&window.__VUE_DEVTOOLS_GLOBAL_HOOK__;/* istanbul ignore next */function isNative(Ctor){return /native code/.test(Ctor.toString());}/**
 * Defer a task to execute it asynchronously.
 */var nextTick=function(){var callbacks=[];var pending=false;var timerFunc;function nextTickHandler(){pending=false;var copies=callbacks.slice(0);callbacks.length=0;for(var i=0;i<copies.length;i++){copies[i]();}}// the nextTick behavior leverages the microtask queue, which can be accessed
// via either native Promise.then or MutationObserver.
// MutationObserver has wider support, however it is seriously bugged in
// UIWebView in iOS >= 9.3.3 when triggered in touch event handlers. It
// completely stops working after triggering a few times... so, if native
// Promise is available, we will use it:
/* istanbul ignore if */if(typeof Promise!=='undefined'&&isNative(Promise)){var p=Promise.resolve();timerFunc=function timerFunc(){p.then(nextTickHandler);// in problematic UIWebViews, Promise.then doesn't completely break, but
// it can get stuck in a weird state where callbacks are pushed into the
// microtask queue but the queue isn't being flushed, until the browser
// needs to do some other work, e.g. handle a timer. Therefore we can
// "force" the microtask queue to be flushed by adding an empty timer.
if(isIOS){setTimeout(noop);}};}else if(typeof MutationObserver!=='undefined'&&(isNative(MutationObserver)||// PhantomJS and iOS 7.x
MutationObserver.toString()==='[object MutationObserverConstructor]')){// use MutationObserver where native Promise is not available,
// e.g. PhantomJS IE11, iOS7, Android 4.4
var counter=1;var observer=new MutationObserver(nextTickHandler);var textNode=document.createTextNode(String(counter));observer.observe(textNode,{characterData:true});timerFunc=function timerFunc(){counter=(counter+1)%2;textNode.data=String(counter);};}else{// fallback to setTimeout
/* istanbul ignore next */timerFunc=function timerFunc(){setTimeout(nextTickHandler,0);};}return function queueNextTick(cb,ctx){var func=ctx?function(){cb.call(ctx);}:cb;callbacks.push(func);if(!pending){pending=true;timerFunc();}};}();var _Set;/* istanbul ignore if */if(typeof Set!=='undefined'&&isNative(Set)){// use native Set when available.
_Set=Set;}else{// a non-standard Set polyfill that only works with primitive keys.
_Set=function(){function Set(){this.set=Object.create(null);}Set.prototype.has=function has(key){return this.set[key]!==undefined;};Set.prototype.add=function add(key){this.set[key]=1;};Set.prototype.clear=function clear(){this.set=Object.create(null);};return Set;}();}/* not type checking this file because flow doesn't play well with Proxy */var hasProxy;var proxyHandlers;var initProxy;{var allowedGlobals=makeMap('Infinity,undefined,NaN,isFinite,isNaN,'+'parseFloat,parseInt,decodeURI,decodeURIComponent,encodeURI,encodeURIComponent,'+'Math,Number,Date,Array,Object,Boolean,String,RegExp,Map,Set,JSON,Intl,'+'require'// for Webpack/Browserify
);hasProxy=typeof Proxy!=='undefined'&&Proxy.toString().match(/native code/);proxyHandlers={has:function has(target,key){var has=key in target;var isAllowed=allowedGlobals(key)||key.charAt(0)==='_';if(!has&&!isAllowed){warn("Property or method \""+key+"\" is not defined on the instance but "+"referenced during render. Make sure to declare reactive data "+"properties in the data option.",target);}return has||!isAllowed;}};initProxy=function initProxy(vm){if(hasProxy){vm._renderProxy=new Proxy(vm,proxyHandlers);}else{vm._renderProxy=vm;}};}/*  */var uid$2=0;/**
 * A dep is an observable that can have multiple
 * directives subscribing to it.
 */var Dep=function Dep(){this.id=uid$2++;this.subs=[];};Dep.prototype.addSub=function addSub(sub){this.subs.push(sub);};Dep.prototype.removeSub=function removeSub(sub){remove$1(this.subs,sub);};Dep.prototype.depend=function depend(){if(Dep.target){Dep.target.addDep(this);}};Dep.prototype.notify=function notify(){// stablize the subscriber list first
var subs=this.subs.slice();for(var i=0,l=subs.length;i<l;i++){subs[i].update();}};// the current target watcher being evaluated.
// this is globally unique because there could be only one
// watcher being evaluated at any time.
Dep.target=null;var targetStack=[];function pushTarget(_target){if(Dep.target){targetStack.push(Dep.target);}Dep.target=_target;}function popTarget(){Dep.target=targetStack.pop();}/*  */var queue=[];var has$1={};var circular={};var waiting=false;var flushing=false;var index=0;/**
 * Reset the scheduler's state.
 */function resetSchedulerState(){queue.length=0;has$1={};{circular={};}waiting=flushing=false;}/**
 * Flush both queues and run the watchers.
 */function flushSchedulerQueue(){flushing=true;// Sort queue before flush.
// This ensures that:
// 1. Components are updated from parent to child. (because parent is always
//    created before the child)
// 2. A component's user watchers are run before its render watcher (because
//    user watchers are created before the render watcher)
// 3. If a component is destroyed during a parent component's watcher run,
//    its watchers can be skipped.
queue.sort(function(a,b){return a.id-b.id;});// do not cache length because more watchers might be pushed
// as we run existing watchers
for(index=0;index<queue.length;index++){var watcher=queue[index];var id=watcher.id;has$1[id]=null;watcher.run();// in dev build, check and stop circular updates.
if("development"!=='production'&&has$1[id]!=null){circular[id]=(circular[id]||0)+1;if(circular[id]>config._maxUpdateCount){warn('You may have an infinite update loop '+(watcher.user?"in watcher with expression \""+watcher.expression+"\"":"in a component render function."),watcher.vm);break;}}}// devtool hook
/* istanbul ignore if */if(devtools&&config.devtools){devtools.emit('flush');}resetSchedulerState();}/**
 * Push a watcher into the watcher queue.
 * Jobs with duplicate IDs will be skipped unless it's
 * pushed when the queue is being flushed.
 */function queueWatcher(watcher){var id=watcher.id;if(has$1[id]==null){has$1[id]=true;if(!flushing){queue.push(watcher);}else{// if already flushing, splice the watcher based on its id
// if already past its id, it will be run next immediately.
var i=queue.length-1;while(i>=0&&queue[i].id>watcher.id){i--;}queue.splice(Math.max(i,index)+1,0,watcher);}// queue the flush
if(!waiting){waiting=true;nextTick(flushSchedulerQueue);}}}/*  */var uid$1=0;/**
 * A watcher parses an expression, collects dependencies,
 * and fires callback when the expression value changes.
 * This is used for both the $watch() api and directives.
 */var Watcher=function Watcher(vm,expOrFn,cb,options){if(options===void 0)options={};this.vm=vm;vm._watchers.push(this);// options
this.deep=!!options.deep;this.user=!!options.user;this.lazy=!!options.lazy;this.sync=!!options.sync;this.expression=expOrFn.toString();this.cb=cb;this.id=++uid$1;// uid for batching
this.active=true;this.dirty=this.lazy;// for lazy watchers
this.deps=[];this.newDeps=[];this.depIds=new _Set();this.newDepIds=new _Set();// parse expression for getter
if(typeof expOrFn==='function'){this.getter=expOrFn;}else{this.getter=parsePath(expOrFn);if(!this.getter){this.getter=function(){};"development"!=='production'&&warn("Failed watching path: \""+expOrFn+"\" "+'Watcher only accepts simple dot-delimited paths. '+'For full control, use a function instead.',vm);}}this.value=this.lazy?undefined:this.get();};/**
 * Evaluate the getter, and re-collect dependencies.
 */Watcher.prototype.get=function get(){pushTarget(this);var value=this.getter.call(this.vm,this.vm);// "touch" every property so they are all tracked as
// dependencies for deep watching
if(this.deep){traverse(value);}popTarget();this.cleanupDeps();return value;};/**
 * Add a dependency to this directive.
 */Watcher.prototype.addDep=function addDep(dep){var id=dep.id;if(!this.newDepIds.has(id)){this.newDepIds.add(id);this.newDeps.push(dep);if(!this.depIds.has(id)){dep.addSub(this);}}};/**
 * Clean up for dependency collection.
 */Watcher.prototype.cleanupDeps=function cleanupDeps(){var this$1=this;var i=this.deps.length;while(i--){var dep=this$1.deps[i];if(!this$1.newDepIds.has(dep.id)){dep.removeSub(this$1);}}var tmp=this.depIds;this.depIds=this.newDepIds;this.newDepIds=tmp;this.newDepIds.clear();tmp=this.deps;this.deps=this.newDeps;this.newDeps=tmp;this.newDeps.length=0;};/**
 * Subscriber interface.
 * Will be called when a dependency changes.
 */Watcher.prototype.update=function update(){/* istanbul ignore else */if(this.lazy){this.dirty=true;}else if(this.sync){this.run();}else{queueWatcher(this);}};/**
 * Scheduler job interface.
 * Will be called by the scheduler.
 */Watcher.prototype.run=function run(){if(this.active){var value=this.get();if(value!==this.value||// Deep watchers and watchers on Object/Arrays should fire even
// when the value is the same, because the value may
// have mutated.
isObject(value)||this.deep){// set new value
var oldValue=this.value;this.value=value;if(this.user){try{this.cb.call(this.vm,value,oldValue);}catch(e){"development"!=='production'&&warn("Error in watcher \""+this.expression+"\"",this.vm);/* istanbul ignore else */if(config.errorHandler){config.errorHandler.call(null,e,this.vm);}else{throw e;}}}else{this.cb.call(this.vm,value,oldValue);}}}};/**
 * Evaluate the value of the watcher.
 * This only gets called for lazy watchers.
 */Watcher.prototype.evaluate=function evaluate(){this.value=this.get();this.dirty=false;};/**
 * Depend on all deps collected by this watcher.
 */Watcher.prototype.depend=function depend(){var this$1=this;var i=this.deps.length;while(i--){this$1.deps[i].depend();}};/**
 * Remove self from all dependencies' subcriber list.
 */Watcher.prototype.teardown=function teardown(){var this$1=this;if(this.active){// remove self from vm's watcher list
// this is a somewhat expensive operation so we skip it
// if the vm is being destroyed or is performing a v-for
// re-render (the watcher list is then filtered by v-for).
if(!this.vm._isBeingDestroyed&&!this.vm._vForRemoving){remove$1(this.vm._watchers,this);}var i=this.deps.length;while(i--){this$1.deps[i].removeSub(this$1);}this.active=false;}};/**
 * Recursively traverse an object to evoke all converted
 * getters, so that every nested property inside the object
 * is collected as a "deep" dependency.
 */var seenObjects=new _Set();function traverse(val,seen){var i,keys;if(!seen){seen=seenObjects;seen.clear();}var isA=Array.isArray(val);var isO=isObject(val);if((isA||isO)&&Object.isExtensible(val)){if(val.__ob__){var depId=val.__ob__.dep.id;if(seen.has(depId)){return;}else{seen.add(depId);}}if(isA){i=val.length;while(i--){traverse(val[i],seen);}}else if(isO){keys=Object.keys(val);i=keys.length;while(i--){traverse(val[keys[i]],seen);}}}}/*
 * not type checking this file because flow doesn't play well with
 * dynamically accessing methods on Array prototype
 */var arrayProto=Array.prototype;var arrayMethods=Object.create(arrayProto);['push','pop','shift','unshift','splice','sort','reverse'].forEach(function(method){// cache original method
var original=arrayProto[method];def(arrayMethods,method,function mutator(){var arguments$1=arguments;// avoid leaking arguments:
// http://jsperf.com/closure-with-arguments
var i=arguments.length;var args=new Array(i);while(i--){args[i]=arguments$1[i];}var result=original.apply(this,args);var ob=this.__ob__;var inserted;switch(method){case'push':inserted=args;break;case'unshift':inserted=args;break;case'splice':inserted=args.slice(2);break;}if(inserted){ob.observeArray(inserted);}// notify change
ob.dep.notify();return result;});});/*  */var arrayKeys=Object.getOwnPropertyNames(arrayMethods);/**
 * By default, when a reactive property is set, the new value is
 * also converted to become reactive. However when passing down props,
 * we don't want to force conversion because the value may be a nested value
 * under a frozen data structure. Converting it would defeat the optimization.
 */var observerState={shouldConvert:true,isSettingProps:false};/**
 * Observer class that are attached to each observed
 * object. Once attached, the observer converts target
 * object's property keys into getter/setters that
 * collect dependencies and dispatches updates.
 */var Observer=function Observer(value){this.value=value;this.dep=new Dep();this.vmCount=0;def(value,'__ob__',this);if(Array.isArray(value)){var augment=hasProto?protoAugment:copyAugment;augment(value,arrayMethods,arrayKeys);this.observeArray(value);}else{this.walk(value);}};/**
 * Walk through each property and convert them into
 * getter/setters. This method should only be called when
 * value type is Object.
 */Observer.prototype.walk=function walk(obj){var keys=Object.keys(obj);for(var i=0;i<keys.length;i++){defineReactive$$1(obj,keys[i],obj[keys[i]]);}};/**
 * Observe a list of Array items.
 */Observer.prototype.observeArray=function observeArray(items){for(var i=0,l=items.length;i<l;i++){observe(items[i]);}};// helpers
/**
 * Augment an target Object or Array by intercepting
 * the prototype chain using __proto__
 */function protoAugment(target,src){/* eslint-disable no-proto */target.__proto__=src;/* eslint-enable no-proto */}/**
 * Augment an target Object or Array by defining
 * hidden properties.
 *
 * istanbul ignore next
 */function copyAugment(target,src,keys){for(var i=0,l=keys.length;i<l;i++){var key=keys[i];def(target,key,src[key]);}}/**
 * Attempt to create an observer instance for a value,
 * returns the new observer if successfully observed,
 * or the existing observer if the value already has one.
 */function observe(value){if(!isObject(value)){return;}var ob;if(hasOwn(value,'__ob__')&&value.__ob__ instanceof Observer){ob=value.__ob__;}else if(observerState.shouldConvert&&!config._isServer&&(Array.isArray(value)||isPlainObject(value))&&Object.isExtensible(value)&&!value._isVue){ob=new Observer(value);}return ob;}/**
 * Define a reactive property on an Object.
 */function defineReactive$$1(obj,key,val,customSetter){var dep=new Dep();var property=Object.getOwnPropertyDescriptor(obj,key);if(property&&property.configurable===false){return;}// cater for pre-defined getter/setters
var getter=property&&property.get;var setter=property&&property.set;var childOb=observe(val);Object.defineProperty(obj,key,{enumerable:true,configurable:true,get:function reactiveGetter(){var value=getter?getter.call(obj):val;if(Dep.target){dep.depend();if(childOb){childOb.dep.depend();}if(Array.isArray(value)){dependArray(value);}}return value;},set:function reactiveSetter(newVal){var value=getter?getter.call(obj):val;if(newVal===value){return;}if("development"!=='production'&&customSetter){customSetter();}if(setter){setter.call(obj,newVal);}else{val=newVal;}childOb=observe(newVal);dep.notify();}});}/**
 * Set a property on an object. Adds the new property and
 * triggers change notification if the property doesn't
 * already exist.
 */function set(obj,key,val){if(Array.isArray(obj)){obj.splice(key,1,val);return val;}if(hasOwn(obj,key)){obj[key]=val;return;}var ob=obj.__ob__;if(obj._isVue||ob&&ob.vmCount){"development"!=='production'&&warn('Avoid adding reactive properties to a Vue instance or its root $data '+'at runtime - declare it upfront in the data option.');return;}if(!ob){obj[key]=val;return;}defineReactive$$1(ob.value,key,val);ob.dep.notify();return val;}/**
 * Delete a property and trigger change if necessary.
 */function del(obj,key){var ob=obj.__ob__;if(obj._isVue||ob&&ob.vmCount){"development"!=='production'&&warn('Avoid deleting properties on a Vue instance or its root $data '+'- just set it to null.');return;}if(!hasOwn(obj,key)){return;}delete obj[key];if(!ob){return;}ob.dep.notify();}/**
 * Collect dependencies on array elements when the array is touched, since
 * we cannot intercept array element access like property getters.
 */function dependArray(value){for(var e=void 0,i=0,l=value.length;i<l;i++){e=value[i];e&&e.__ob__&&e.__ob__.dep.depend();if(Array.isArray(e)){dependArray(e);}}}/*  */function initState(vm){vm._watchers=[];initProps(vm);initData(vm);initComputed(vm);initMethods(vm);initWatch(vm);}function initProps(vm){var props=vm.$options.props;if(props){var propsData=vm.$options.propsData||{};var keys=vm.$options._propKeys=Object.keys(props);var isRoot=!vm.$parent;// root instance props should be converted
observerState.shouldConvert=isRoot;var loop=function loop(i){var key=keys[i];/* istanbul ignore else */{defineReactive$$1(vm,key,validateProp(key,props,propsData,vm),function(){if(vm.$parent&&!observerState.isSettingProps){warn("Avoid mutating a prop directly since the value will be "+"overwritten whenever the parent component re-renders. "+"Instead, use a data or computed property based on the prop's "+"value. Prop being mutated: \""+key+"\"",vm);}});}};for(var i=0;i<keys.length;i++){loop(i);}observerState.shouldConvert=true;}}function initData(vm){var data=vm.$options.data;data=vm._data=typeof data==='function'?data.call(vm):data||{};if(!isPlainObject(data)){data={};"development"!=='production'&&warn('data functions should return an object.',vm);}// proxy data on instance
var keys=Object.keys(data);var props=vm.$options.props;var i=keys.length;while(i--){if(props&&hasOwn(props,keys[i])){"development"!=='production'&&warn("The data property \""+keys[i]+"\" is already declared as a prop. "+"Use prop default value instead.",vm);}else{proxy(vm,keys[i]);}}// observe data
observe(data);data.__ob__&&data.__ob__.vmCount++;}var computedSharedDefinition={enumerable:true,configurable:true,get:noop,set:noop};function initComputed(vm){var computed=vm.$options.computed;if(computed){for(var key in computed){var userDef=computed[key];if(typeof userDef==='function'){computedSharedDefinition.get=makeComputedGetter(userDef,vm);computedSharedDefinition.set=noop;}else{computedSharedDefinition.get=userDef.get?userDef.cache!==false?makeComputedGetter(userDef.get,vm):bind$1(userDef.get,vm):noop;computedSharedDefinition.set=userDef.set?bind$1(userDef.set,vm):noop;}Object.defineProperty(vm,key,computedSharedDefinition);}}}function makeComputedGetter(getter,owner){var watcher=new Watcher(owner,getter,noop,{lazy:true});return function computedGetter(){if(watcher.dirty){watcher.evaluate();}if(Dep.target){watcher.depend();}return watcher.value;};}function initMethods(vm){var methods=vm.$options.methods;if(methods){for(var key in methods){vm[key]=methods[key]==null?noop:bind$1(methods[key],vm);if("development"!=='production'&&methods[key]==null){warn("method \""+key+"\" has an undefined value in the component definition. "+"Did you reference the function correctly?",vm);}}}}function initWatch(vm){var watch=vm.$options.watch;if(watch){for(var key in watch){var handler=watch[key];if(Array.isArray(handler)){for(var i=0;i<handler.length;i++){createWatcher(vm,key,handler[i]);}}else{createWatcher(vm,key,handler);}}}}function createWatcher(vm,key,handler){var options;if(isPlainObject(handler)){options=handler;handler=handler.handler;}if(typeof handler==='string'){handler=vm[handler];}vm.$watch(key,handler,options);}function stateMixin(Vue){// flow somehow has problems with directly declared definition object
// when using Object.defineProperty, so we have to procedurally build up
// the object here.
var dataDef={};dataDef.get=function(){return this._data;};{dataDef.set=function(newData){warn('Avoid replacing instance root $data. '+'Use nested data properties instead.',this);};}Object.defineProperty(Vue.prototype,'$data',dataDef);Vue.prototype.$set=set;Vue.prototype.$delete=del;Vue.prototype.$watch=function(expOrFn,cb,options){var vm=this;options=options||{};options.user=true;var watcher=new Watcher(vm,expOrFn,cb,options);if(options.immediate){cb.call(vm,watcher.value);}return function unwatchFn(){watcher.teardown();};};}function proxy(vm,key){if(!isReserved(key)){Object.defineProperty(vm,key,{configurable:true,enumerable:true,get:function proxyGetter(){return vm._data[key];},set:function proxySetter(val){vm._data[key]=val;}});}}/*  */var VNode=function VNode(tag,data,children,text,elm,ns,context,componentOptions){this.tag=tag;this.data=data;this.children=children;this.text=text;this.elm=elm;this.ns=ns;this.context=context;this.functionalContext=undefined;this.key=data&&data.key;this.componentOptions=componentOptions;this.child=undefined;this.parent=undefined;this.raw=false;this.isStatic=false;this.isRootInsert=true;this.isComment=false;this.isCloned=false;};var emptyVNode=function emptyVNode(){var node=new VNode();node.text='';node.isComment=true;return node;};// optimized shallow clone
// used for static nodes and slot nodes because they may be reused across
// multiple renders, cloning them avoids errors when DOM manipulations rely
// on their elm reference.
function cloneVNode(vnode){var cloned=new VNode(vnode.tag,vnode.data,vnode.children,vnode.text,vnode.elm,vnode.ns,vnode.context,vnode.componentOptions);cloned.isStatic=vnode.isStatic;cloned.key=vnode.key;cloned.isCloned=true;return cloned;}function cloneVNodes(vnodes){var res=new Array(vnodes.length);for(var i=0;i<vnodes.length;i++){res[i]=cloneVNode(vnodes[i]);}return res;}/*  */function mergeVNodeHook(def,hookKey,hook,key){key=key+hookKey;var injectedHash=def.__injected||(def.__injected={});if(!injectedHash[key]){injectedHash[key]=true;var oldHook=def[hookKey];if(oldHook){def[hookKey]=function(){oldHook.apply(this,arguments);hook.apply(this,arguments);};}else{def[hookKey]=hook;}}}/*  */function updateListeners(on,oldOn,add,remove$$1,vm){var name,cur,old,fn,event,capture;for(name in on){cur=on[name];old=oldOn[name];if(!cur){"development"!=='production'&&warn("Invalid handler for event \""+name+"\": got "+String(cur),vm);}else if(!old){capture=name.charAt(0)==='!';event=capture?name.slice(1):name;if(Array.isArray(cur)){add(event,cur.invoker=arrInvoker(cur),capture);}else{if(!cur.invoker){fn=cur;cur=on[name]={};cur.fn=fn;cur.invoker=fnInvoker(cur);}add(event,cur.invoker,capture);}}else if(cur!==old){if(Array.isArray(old)){old.length=cur.length;for(var i=0;i<old.length;i++){old[i]=cur[i];}on[name]=old;}else{old.fn=cur;on[name]=old;}}}for(name in oldOn){if(!on[name]){event=name.charAt(0)==='!'?name.slice(1):name;remove$$1(event,oldOn[name].invoker);}}}function arrInvoker(arr){return function(ev){var arguments$1=arguments;var single=arguments.length===1;for(var i=0;i<arr.length;i++){single?arr[i](ev):arr[i].apply(null,arguments$1);}};}function fnInvoker(o){return function(ev){var single=arguments.length===1;single?o.fn(ev):o.fn.apply(null,arguments);};}/*  */function normalizeChildren(children,ns,nestedIndex){if(isPrimitive(children)){return[createTextVNode(children)];}if(Array.isArray(children)){var res=[];for(var i=0,l=children.length;i<l;i++){var c=children[i];var last=res[res.length-1];//  nested
if(Array.isArray(c)){res.push.apply(res,normalizeChildren(c,ns,(nestedIndex||'')+"_"+i));}else if(isPrimitive(c)){if(last&&last.text){last.text+=String(c);}else if(c!==''){// convert primitive to vnode
res.push(createTextVNode(c));}}else if(c instanceof VNode){if(c.text&&last&&last.text){last.text+=c.text;}else{// inherit parent namespace
if(ns){applyNS(c,ns);}// default key for nested array children (likely generated by v-for)
if(c.tag&&c.key==null&&nestedIndex!=null){c.key="__vlist"+nestedIndex+"_"+i+"__";}res.push(c);}}}return res;}}function createTextVNode(val){return new VNode(undefined,undefined,undefined,String(val));}function applyNS(vnode,ns){if(vnode.tag&&!vnode.ns){vnode.ns=ns;if(vnode.children){for(var i=0,l=vnode.children.length;i<l;i++){applyNS(vnode.children[i],ns);}}}}/*  */function getFirstComponentChild(children){return children&&children.filter(function(c){return c&&c.componentOptions;})[0];}/*  */var activeInstance=null;function initLifecycle(vm){var options=vm.$options;// locate first non-abstract parent
var parent=options.parent;if(parent&&!options.abstract){while(parent.$options.abstract&&parent.$parent){parent=parent.$parent;}parent.$children.push(vm);}vm.$parent=parent;vm.$root=parent?parent.$root:vm;vm.$children=[];vm.$refs={};vm._watcher=null;vm._inactive=false;vm._isMounted=false;vm._isDestroyed=false;vm._isBeingDestroyed=false;}function lifecycleMixin(Vue){Vue.prototype._mount=function(el,hydrating){var vm=this;vm.$el=el;if(!vm.$options.render){vm.$options.render=emptyVNode;{/* istanbul ignore if */if(vm.$options.template){warn('You are using the runtime-only build of Vue where the template '+'option is not available. Either pre-compile the templates into '+'render functions, or use the compiler-included build.',vm);}else{warn('Failed to mount component: template or render function not defined.',vm);}}}callHook(vm,'beforeMount');vm._watcher=new Watcher(vm,function(){vm._update(vm._render(),hydrating);},noop);hydrating=false;// manually mounted instance, call mounted on self
// mounted is called for render-created child components in its inserted hook
if(vm.$vnode==null){vm._isMounted=true;callHook(vm,'mounted');}return vm;};Vue.prototype._update=function(vnode,hydrating){var vm=this;if(vm._isMounted){callHook(vm,'beforeUpdate');}var prevEl=vm.$el;var prevActiveInstance=activeInstance;activeInstance=vm;var prevVnode=vm._vnode;vm._vnode=vnode;if(!prevVnode){// Vue.prototype.__patch__ is injected in entry points
// based on the rendering backend used.
vm.$el=vm.__patch__(vm.$el,vnode,hydrating);}else{vm.$el=vm.__patch__(prevVnode,vnode);}activeInstance=prevActiveInstance;// update __vue__ reference
if(prevEl){prevEl.__vue__=null;}if(vm.$el){vm.$el.__vue__=vm;}// if parent is an HOC, update its $el as well
if(vm.$vnode&&vm.$parent&&vm.$vnode===vm.$parent._vnode){vm.$parent.$el=vm.$el;}if(vm._isMounted){callHook(vm,'updated');}};Vue.prototype._updateFromParent=function(propsData,listeners,parentVnode,renderChildren){var vm=this;var hasChildren=!!(vm.$options._renderChildren||renderChildren);vm.$options._parentVnode=parentVnode;vm.$options._renderChildren=renderChildren;// update props
if(propsData&&vm.$options.props){observerState.shouldConvert=false;{observerState.isSettingProps=true;}var propKeys=vm.$options._propKeys||[];for(var i=0;i<propKeys.length;i++){var key=propKeys[i];vm[key]=validateProp(key,vm.$options.props,propsData,vm);}observerState.shouldConvert=true;{observerState.isSettingProps=false;}}// update listeners
if(listeners){var oldListeners=vm.$options._parentListeners;vm.$options._parentListeners=listeners;vm._updateListeners(listeners,oldListeners);}// resolve slots + force update if has children
if(hasChildren){vm.$slots=resolveSlots(renderChildren,vm._renderContext);vm.$forceUpdate();}};Vue.prototype.$forceUpdate=function(){var vm=this;if(vm._watcher){vm._watcher.update();}};Vue.prototype.$destroy=function(){var vm=this;if(vm._isBeingDestroyed){return;}callHook(vm,'beforeDestroy');vm._isBeingDestroyed=true;// remove self from parent
var parent=vm.$parent;if(parent&&!parent._isBeingDestroyed&&!vm.$options.abstract){remove$1(parent.$children,vm);}// teardown watchers
if(vm._watcher){vm._watcher.teardown();}var i=vm._watchers.length;while(i--){vm._watchers[i].teardown();}// remove reference from data ob
// frozen object may not have observer.
if(vm._data.__ob__){vm._data.__ob__.vmCount--;}// call the last hook...
vm._isDestroyed=true;callHook(vm,'destroyed');// turn off all instance listeners.
vm.$off();// remove __vue__ reference
if(vm.$el){vm.$el.__vue__=null;}// invoke destroy hooks on current rendered tree
vm.__patch__(vm._vnode,null);};}function callHook(vm,hook){var handlers=vm.$options[hook];if(handlers){for(var i=0,j=handlers.length;i<j;i++){handlers[i].call(vm);}}vm.$emit('hook:'+hook);}/*  */var hooks={init:init,prepatch:prepatch,insert:insert,destroy:destroy$1};var hooksToMerge=Object.keys(hooks);function createComponent(Ctor,data,context,children,tag){if(!Ctor){return;}if(isObject(Ctor)){Ctor=Vue$3.extend(Ctor);}if(typeof Ctor!=='function'){{warn("Invalid Component definition: "+String(Ctor),context);}return;}// async component
if(!Ctor.cid){if(Ctor.resolved){Ctor=Ctor.resolved;}else{Ctor=resolveAsyncComponent(Ctor,function(){// it's ok to queue this on every render because
// $forceUpdate is buffered by the scheduler.
context.$forceUpdate();});if(!Ctor){// return nothing if this is indeed an async component
// wait for the callback to trigger parent update.
return;}}}data=data||{};// extract props
var propsData=extractProps(data,Ctor);// functional component
if(Ctor.options.functional){return createFunctionalComponent(Ctor,propsData,data,context,children);}// extract listeners, since these needs to be treated as
// child component listeners instead of DOM listeners
var listeners=data.on;// replace with listeners with .native modifier
data.on=data.nativeOn;if(Ctor.options.abstract){// abstract components do not keep anything
// other than props & listeners
data={};}// merge component management hooks onto the placeholder node
mergeHooks(data);// return a placeholder vnode
var name=Ctor.options.name||tag;var vnode=new VNode("vue-component-"+Ctor.cid+(name?"-"+name:''),data,undefined,undefined,undefined,undefined,context,{Ctor:Ctor,propsData:propsData,listeners:listeners,tag:tag,children:children});return vnode;}function createFunctionalComponent(Ctor,propsData,data,context,children){var props={};var propOptions=Ctor.options.props;if(propOptions){for(var key in propOptions){props[key]=validateProp(key,propOptions,propsData);}}var vnode=Ctor.options.render.call(null,// ensure the createElement function in functional components
// gets a unique context - this is necessary for correct named slot check
bind$1(createElement,{_self:Object.create(context)}),{props:props,data:data,parent:context,children:normalizeChildren(children),slots:function slots(){return resolveSlots(children,context);}});if(vnode instanceof VNode){vnode.functionalContext=context;if(data.slot){(vnode.data||(vnode.data={})).slot=data.slot;}}return vnode;}function createComponentInstanceForVnode(vnode,// we know it's MountedComponentVNode but flow doesn't
parent// activeInstance in lifecycle state
){var vnodeComponentOptions=vnode.componentOptions;var options={_isComponent:true,parent:parent,propsData:vnodeComponentOptions.propsData,_componentTag:vnodeComponentOptions.tag,_parentVnode:vnode,_parentListeners:vnodeComponentOptions.listeners,_renderChildren:vnodeComponentOptions.children};// check inline-template render functions
var inlineTemplate=vnode.data.inlineTemplate;if(inlineTemplate){options.render=inlineTemplate.render;options.staticRenderFns=inlineTemplate.staticRenderFns;}return new vnodeComponentOptions.Ctor(options);}function init(vnode,hydrating){if(!vnode.child||vnode.child._isDestroyed){var child=vnode.child=createComponentInstanceForVnode(vnode,activeInstance);child.$mount(hydrating?vnode.elm:undefined,hydrating);}}function prepatch(oldVnode,vnode){var options=vnode.componentOptions;var child=vnode.child=oldVnode.child;child._updateFromParent(options.propsData,// updated props
options.listeners,// updated listeners
vnode,// new parent vnode
options.children// new children
);}function insert(vnode){if(!vnode.child._isMounted){vnode.child._isMounted=true;callHook(vnode.child,'mounted');}if(vnode.data.keepAlive){vnode.child._inactive=false;callHook(vnode.child,'activated');}}function destroy$1(vnode){if(!vnode.child._isDestroyed){if(!vnode.data.keepAlive){vnode.child.$destroy();}else{vnode.child._inactive=true;callHook(vnode.child,'deactivated');}}}function resolveAsyncComponent(factory,cb){if(factory.requested){// pool callbacks
factory.pendingCallbacks.push(cb);}else{factory.requested=true;var cbs=factory.pendingCallbacks=[cb];var sync=true;var resolve=function resolve(res){if(isObject(res)){res=Vue$3.extend(res);}// cache resolved
factory.resolved=res;// invoke callbacks only if this is not a synchronous resolve
// (async resolves are shimmed as synchronous during SSR)
if(!sync){for(var i=0,l=cbs.length;i<l;i++){cbs[i](res);}}};var reject=function reject(reason){"development"!=='production'&&warn("Failed to resolve async component: "+String(factory)+(reason?"\nReason: "+reason:''));};var res=factory(resolve,reject);// handle promise
if(res&&typeof res.then==='function'&&!factory.resolved){res.then(resolve,reject);}sync=false;// return in case resolved synchronously
return factory.resolved;}}function extractProps(data,Ctor){// we are only extrating raw values here.
// validation and default values are handled in the child
// component itself.
var propOptions=Ctor.options.props;if(!propOptions){return;}var res={};var attrs=data.attrs;var props=data.props;var domProps=data.domProps;if(attrs||props||domProps){for(var key in propOptions){var altKey=hyphenate(key);checkProp(res,props,key,altKey,true)||checkProp(res,attrs,key,altKey)||checkProp(res,domProps,key,altKey);}}return res;}function checkProp(res,hash,key,altKey,preserve){if(hash){if(hasOwn(hash,key)){res[key]=hash[key];if(!preserve){delete hash[key];}return true;}else if(hasOwn(hash,altKey)){res[key]=hash[altKey];if(!preserve){delete hash[altKey];}return true;}}return false;}function mergeHooks(data){if(!data.hook){data.hook={};}for(var i=0;i<hooksToMerge.length;i++){var key=hooksToMerge[i];var fromParent=data.hook[key];var ours=hooks[key];data.hook[key]=fromParent?mergeHook$1(ours,fromParent):ours;}}function mergeHook$1(a,b){// since all hooks have at most two args, use fixed args
// to avoid having to use fn.apply().
return function(_,__){a(_,__);b(_,__);};}/*  */// wrapper function for providing a more flexible interface
// without getting yelled at by flow
function createElement(tag,data,children){if(data&&(Array.isArray(data)||(typeof data==='undefined'?'undefined':_typeof(data))!=='object')){children=data;data=undefined;}// make sure to use real instance instead of proxy as context
return _createElement(this._self,tag,data,children);}function _createElement(context,tag,data,children){if(data&&data.__ob__){"development"!=='production'&&warn("Avoid using observed data object as vnode data: "+JSON.stringify(data)+"\n"+'Always create fresh vnode data objects in each render!',context);return;}if(!tag){// in case of component :is set to falsy value
return emptyVNode();}if(typeof tag==='string'){var Ctor;var ns=config.getTagNamespace(tag);if(config.isReservedTag(tag)){// platform built-in elements
return new VNode(tag,data,normalizeChildren(children,ns),undefined,undefined,ns,context);}else if(Ctor=resolveAsset(context.$options,'components',tag)){// component
return createComponent(Ctor,data,context,children,tag);}else{// unknown or unlisted namespaced elements
// check at runtime because it may get assigned a namespace when its
// parent normalizes children
return new VNode(tag,data,normalizeChildren(children,ns),undefined,undefined,ns,context);}}else{// direct component options / constructor
return createComponent(tag,data,context,children);}}/*  */function initRender(vm){vm.$vnode=null;// the placeholder node in parent tree
vm._vnode=null;// the root of the child tree
vm._staticTrees=null;vm._renderContext=vm.$options._parentVnode&&vm.$options._parentVnode.context;vm.$slots=resolveSlots(vm.$options._renderChildren,vm._renderContext);// bind the public createElement fn to this instance
// so that we get proper render context inside it.
vm.$createElement=bind$1(createElement,vm);if(vm.$options.el){vm.$mount(vm.$options.el);}}function renderMixin(Vue){Vue.prototype.$nextTick=function(fn){nextTick(fn,this);};Vue.prototype._render=function(){var vm=this;var ref=vm.$options;var render=ref.render;var staticRenderFns=ref.staticRenderFns;var _parentVnode=ref._parentVnode;if(vm._isMounted){// clone slot nodes on re-renders
for(var key in vm.$slots){vm.$slots[key]=cloneVNodes(vm.$slots[key]);}}if(staticRenderFns&&!vm._staticTrees){vm._staticTrees=[];}// set parent vnode. this allows render functions to have access
// to the data on the placeholder node.
vm.$vnode=_parentVnode;// render self
var vnode;try{vnode=render.call(vm._renderProxy,vm.$createElement);}catch(e){{warn("Error when rendering "+formatComponentName(vm)+":");}/* istanbul ignore else */if(config.errorHandler){config.errorHandler.call(null,e,vm);}else{if(config._isServer){throw e;}else{setTimeout(function(){throw e;},0);}}// return previous vnode to prevent render error causing blank component
vnode=vm._vnode;}// return empty vnode in case the render function errored out
if(!(vnode instanceof VNode)){if("development"!=='production'&&Array.isArray(vnode)){warn('Multiple root nodes returned from render function. Render function '+'should return a single root node.',vm);}vnode=emptyVNode();}// set parent
vnode.parent=_parentVnode;return vnode;};// shorthands used in render functions
Vue.prototype._h=createElement;// toString for mustaches
Vue.prototype._s=_toString;// number conversion
Vue.prototype._n=toNumber;// empty vnode
Vue.prototype._e=emptyVNode;// loose equal
Vue.prototype._q=looseEqual;// loose indexOf
Vue.prototype._i=looseIndexOf;// render static tree by index
Vue.prototype._m=function renderStatic(index,isInFor){var tree=this._staticTrees[index];// if has already-rendered static tree and not inside v-for,
// we can reuse the same tree by doing a shallow clone.
if(tree&&!isInFor){return Array.isArray(tree)?cloneVNodes(tree):cloneVNode(tree);}// otherwise, render a fresh tree.
tree=this._staticTrees[index]=this.$options.staticRenderFns[index].call(this._renderProxy);if(Array.isArray(tree)){for(var i=0;i<tree.length;i++){if(typeof tree[i]!=='string'){tree[i].isStatic=true;tree[i].key="__static__"+index+"_"+i;}}}else{tree.isStatic=true;tree.key="__static__"+index;}return tree;};// filter resolution helper
var identity=function identity(_){return _;};Vue.prototype._f=function resolveFilter(id){return resolveAsset(this.$options,'filters',id,true)||identity;};// render v-for
Vue.prototype._l=function renderList(val,render){var ret,i,l,keys,key;if(Array.isArray(val)){ret=new Array(val.length);for(i=0,l=val.length;i<l;i++){ret[i]=render(val[i],i);}}else if(typeof val==='number'){ret=new Array(val);for(i=0;i<val;i++){ret[i]=render(i+1,i);}}else if(isObject(val)){keys=Object.keys(val);ret=new Array(keys.length);for(i=0,l=keys.length;i<l;i++){key=keys[i];ret[i]=render(val[key],key,i);}}return ret;};// renderSlot
Vue.prototype._t=function(name,fallback){var slotNodes=this.$slots[name];// warn duplicate slot usage
if(slotNodes&&"development"!=='production'){slotNodes._rendered&&warn("Duplicate presence of slot \""+name+"\" found in the same render tree "+"- this will likely cause render errors.",this);slotNodes._rendered=true;}return slotNodes||fallback;};// apply v-bind object
Vue.prototype._b=function bindProps(data,value,asProp){if(value){if(!isObject(value)){"development"!=='production'&&warn('v-bind without argument expects an Object or Array value',this);}else{if(Array.isArray(value)){value=toObject(value);}for(var key in value){if(key==='class'||key==='style'){data[key]=value[key];}else{var hash=asProp||config.mustUseProp(key)?data.domProps||(data.domProps={}):data.attrs||(data.attrs={});hash[key]=value[key];}}}}return data;};// expose v-on keyCodes
Vue.prototype._k=function getKeyCodes(key){return config.keyCodes[key];};}function resolveSlots(renderChildren,context){var slots={};if(!renderChildren){return slots;}var children=normalizeChildren(renderChildren)||[];var defaultSlot=[];var name,child;for(var i=0,l=children.length;i<l;i++){child=children[i];// named slots should only be respected if the vnode was rendered in the
// same context.
if((child.context===context||child.functionalContext===context)&&child.data&&(name=child.data.slot)){var slot=slots[name]||(slots[name]=[]);if(child.tag==='template'){slot.push.apply(slot,child.children);}else{slot.push(child);}}else{defaultSlot.push(child);}}// ignore single whitespace
if(defaultSlot.length&&!(defaultSlot.length===1&&(defaultSlot[0].text===' '||defaultSlot[0].isComment))){slots.default=defaultSlot;}return slots;}/*  */function initEvents(vm){vm._events=Object.create(null);// init parent attached events
var listeners=vm.$options._parentListeners;var on=bind$1(vm.$on,vm);var off=bind$1(vm.$off,vm);vm._updateListeners=function(listeners,oldListeners){updateListeners(listeners,oldListeners||{},on,off,vm);};if(listeners){vm._updateListeners(listeners);}}function eventsMixin(Vue){Vue.prototype.$on=function(event,fn){var vm=this;(vm._events[event]||(vm._events[event]=[])).push(fn);return vm;};Vue.prototype.$once=function(event,fn){var vm=this;function on(){vm.$off(event,on);fn.apply(vm,arguments);}on.fn=fn;vm.$on(event,on);return vm;};Vue.prototype.$off=function(event,fn){var vm=this;// all
if(!arguments.length){vm._events=Object.create(null);return vm;}// specific event
var cbs=vm._events[event];if(!cbs){return vm;}if(arguments.length===1){vm._events[event]=null;return vm;}// specific handler
var cb;var i=cbs.length;while(i--){cb=cbs[i];if(cb===fn||cb.fn===fn){cbs.splice(i,1);break;}}return vm;};Vue.prototype.$emit=function(event){var vm=this;var cbs=vm._events[event];if(cbs){cbs=cbs.length>1?toArray(cbs):cbs;var args=toArray(arguments,1);for(var i=0,l=cbs.length;i<l;i++){cbs[i].apply(vm,args);}}return vm;};}/*  */var uid=0;function initMixin(Vue){Vue.prototype._init=function(options){var vm=this;// a uid
vm._uid=uid++;// a flag to avoid this being observed
vm._isVue=true;// merge options
if(options&&options._isComponent){// optimize internal component instantiation
// since dynamic options merging is pretty slow, and none of the
// internal component options needs special treatment.
initInternalComponent(vm,options);}else{vm.$options=mergeOptions(resolveConstructorOptions(vm),options||{},vm);}/* istanbul ignore else */{initProxy(vm);}// expose real self
vm._self=vm;initLifecycle(vm);initEvents(vm);callHook(vm,'beforeCreate');initState(vm);callHook(vm,'created');initRender(vm);};function initInternalComponent(vm,options){var opts=vm.$options=Object.create(resolveConstructorOptions(vm));// doing this because it's faster than dynamic enumeration.
opts.parent=options.parent;opts.propsData=options.propsData;opts._parentVnode=options._parentVnode;opts._parentListeners=options._parentListeners;opts._renderChildren=options._renderChildren;opts._componentTag=options._componentTag;if(options.render){opts.render=options.render;opts.staticRenderFns=options.staticRenderFns;}}function resolveConstructorOptions(vm){var Ctor=vm.constructor;var options=Ctor.options;if(Ctor.super){var superOptions=Ctor.super.options;var cachedSuperOptions=Ctor.superOptions;if(superOptions!==cachedSuperOptions){// super option changed
Ctor.superOptions=superOptions;options=Ctor.options=mergeOptions(superOptions,Ctor.extendOptions);if(options.name){options.components[options.name]=Ctor;}}}return options;}}function Vue$3(options){if("development"!=='production'&&!(this instanceof Vue$3)){warn('Vue is a constructor and should be called with the `new` keyword');}this._init(options);}initMixin(Vue$3);stateMixin(Vue$3);eventsMixin(Vue$3);lifecycleMixin(Vue$3);renderMixin(Vue$3);var warn=noop;var formatComponentName;{var hasConsole=typeof console!=='undefined';warn=function warn(msg,vm){if(hasConsole&&!config.silent){console.error("[Vue warn]: "+msg+" "+(vm?formatLocation(formatComponentName(vm)):''));}};formatComponentName=function formatComponentName(vm){if(vm.$root===vm){return'root instance';}var name=vm._isVue?vm.$options.name||vm.$options._componentTag:vm.name;return(name?"component <"+name+">":"anonymous component")+(vm._isVue&&vm.$options.__file?" at "+vm.$options.__file:'');};var formatLocation=function formatLocation(str){if(str==='anonymous component'){str+=" - use the \"name\" option for better debugging messages.";}return"\n(found in "+str+")";};}/*  *//**
 * Option overwriting strategies are functions that handle
 * how to merge a parent option value and a child option
 * value into the final value.
 */var strats=config.optionMergeStrategies;/**
 * Options with restrictions
 */{strats.el=strats.propsData=function(parent,child,vm,key){if(!vm){warn("option \""+key+"\" can only be used during instance "+'creation with the `new` keyword.');}return defaultStrat(parent,child);};}/**
 * Helper that recursively merges two data objects together.
 */function mergeData(to,from){var key,toVal,fromVal;for(key in from){toVal=to[key];fromVal=from[key];if(!hasOwn(to,key)){set(to,key,fromVal);}else if(isObject(toVal)&&isObject(fromVal)){mergeData(toVal,fromVal);}}return to;}/**
 * Data
 */strats.data=function(parentVal,childVal,vm){if(!vm){// in a Vue.extend merge, both should be functions
if(!childVal){return parentVal;}if(typeof childVal!=='function'){"development"!=='production'&&warn('The "data" option should be a function '+'that returns a per-instance value in component '+'definitions.',vm);return parentVal;}if(!parentVal){return childVal;}// when parentVal & childVal are both present,
// we need to return a function that returns the
// merged result of both functions... no need to
// check if parentVal is a function here because
// it has to be a function to pass previous merges.
return function mergedDataFn(){return mergeData(childVal.call(this),parentVal.call(this));};}else if(parentVal||childVal){return function mergedInstanceDataFn(){// instance merge
var instanceData=typeof childVal==='function'?childVal.call(vm):childVal;var defaultData=typeof parentVal==='function'?parentVal.call(vm):undefined;if(instanceData){return mergeData(instanceData,defaultData);}else{return defaultData;}};}};/**
 * Hooks and param attributes are merged as arrays.
 */function mergeHook(parentVal,childVal){return childVal?parentVal?parentVal.concat(childVal):Array.isArray(childVal)?childVal:[childVal]:parentVal;}config._lifecycleHooks.forEach(function(hook){strats[hook]=mergeHook;});/**
 * Assets
 *
 * When a vm is present (instance creation), we need to do
 * a three-way merge between constructor options, instance
 * options and parent options.
 */function mergeAssets(parentVal,childVal){var res=Object.create(parentVal||null);return childVal?extend(res,childVal):res;}config._assetTypes.forEach(function(type){strats[type+'s']=mergeAssets;});/**
 * Watchers.
 *
 * Watchers hashes should not overwrite one
 * another, so we merge them as arrays.
 */strats.watch=function(parentVal,childVal){/* istanbul ignore if */if(!childVal){return parentVal;}if(!parentVal){return childVal;}var ret={};extend(ret,parentVal);for(var key in childVal){var parent=ret[key];var child=childVal[key];if(parent&&!Array.isArray(parent)){parent=[parent];}ret[key]=parent?parent.concat(child):[child];}return ret;};/**
 * Other object hashes.
 */strats.props=strats.methods=strats.computed=function(parentVal,childVal){if(!childVal){return parentVal;}if(!parentVal){return childVal;}var ret=Object.create(null);extend(ret,parentVal);extend(ret,childVal);return ret;};/**
 * Default strategy.
 */var defaultStrat=function defaultStrat(parentVal,childVal){return childVal===undefined?parentVal:childVal;};/**
 * Make sure component options get converted to actual
 * constructors.
 */function normalizeComponents(options){if(options.components){var components=options.components;var def;for(var key in components){var lower=key.toLowerCase();if(isBuiltInTag(lower)||config.isReservedTag(lower)){"development"!=='production'&&warn('Do not use built-in or reserved HTML elements as component '+'id: '+key);continue;}def=components[key];if(isPlainObject(def)){components[key]=Vue$3.extend(def);}}}}/**
 * Ensure all props option syntax are normalized into the
 * Object-based format.
 */function normalizeProps(options){var props=options.props;if(!props){return;}var res={};var i,val,name;if(Array.isArray(props)){i=props.length;while(i--){val=props[i];if(typeof val==='string'){name=camelize(val);res[name]={type:null};}else{warn('props must be strings when using array syntax.');}}}else if(isPlainObject(props)){for(var key in props){val=props[key];name=camelize(key);res[name]=isPlainObject(val)?val:{type:val};}}options.props=res;}/**
 * Normalize raw function directives into object format.
 */function normalizeDirectives(options){var dirs=options.directives;if(dirs){for(var key in dirs){var def=dirs[key];if(typeof def==='function'){dirs[key]={bind:def,update:def};}}}}/**
 * Merge two option objects into a new one.
 * Core utility used in both instantiation and inheritance.
 */function mergeOptions(parent,child,vm){normalizeComponents(child);normalizeProps(child);normalizeDirectives(child);var extendsFrom=child.extends;if(extendsFrom){parent=typeof extendsFrom==='function'?mergeOptions(parent,extendsFrom.options,vm):mergeOptions(parent,extendsFrom,vm);}if(child.mixins){for(var i=0,l=child.mixins.length;i<l;i++){var mixin=child.mixins[i];if(mixin.prototype instanceof Vue$3){mixin=mixin.options;}parent=mergeOptions(parent,mixin,vm);}}var options={};var key;for(key in parent){mergeField(key);}for(key in child){if(!hasOwn(parent,key)){mergeField(key);}}function mergeField(key){var strat=strats[key]||defaultStrat;options[key]=strat(parent[key],child[key],vm,key);}return options;}/**
 * Resolve an asset.
 * This function is used because child instances need access
 * to assets defined in its ancestor chain.
 */function resolveAsset(options,type,id,warnMissing){/* istanbul ignore if */if(typeof id!=='string'){return;}var assets=options[type];var res=assets[id]||// camelCase ID
assets[camelize(id)]||// Pascal Case ID
assets[capitalize(camelize(id))];if("development"!=='production'&&warnMissing&&!res){warn('Failed to resolve '+type.slice(0,-1)+': '+id,options);}return res;}/*  */function validateProp(key,propOptions,propsData,vm){var prop=propOptions[key];var absent=!hasOwn(propsData,key);var value=propsData[key];// handle boolean props
if(isBooleanType(prop.type)){if(absent&&!hasOwn(prop,'default')){value=false;}else if(value===''||value===hyphenate(key)){value=true;}}// check default value
if(value===undefined){value=getPropDefaultValue(vm,prop,key);// since the default value is a fresh copy,
// make sure to observe it.
var prevShouldConvert=observerState.shouldConvert;observerState.shouldConvert=true;observe(value);observerState.shouldConvert=prevShouldConvert;}{assertProp(prop,key,value,vm,absent);}return value;}/**
 * Get the default value of a prop.
 */function getPropDefaultValue(vm,prop,name){// no default, return undefined
if(!hasOwn(prop,'default')){return undefined;}var def=prop.default;// warn against non-factory defaults for Object & Array
if(isObject(def)){"development"!=='production'&&warn('Invalid default value for prop "'+name+'": '+'Props with type Object/Array must use a factory function '+'to return the default value.',vm);}// call factory function for non-Function types
return typeof def==='function'&&prop.type!==Function?def.call(vm):def;}/**
 * Assert whether a prop is valid.
 */function assertProp(prop,name,value,vm,absent){if(prop.required&&absent){warn('Missing required prop: "'+name+'"',vm);return;}if(value==null&&!prop.required){return;}var type=prop.type;var valid=!type||type===true;var expectedTypes=[];if(type){if(!Array.isArray(type)){type=[type];}for(var i=0;i<type.length&&!valid;i++){var assertedType=assertType(value,type[i]);expectedTypes.push(assertedType.expectedType);valid=assertedType.valid;}}if(!valid){warn('Invalid prop: type check failed for prop "'+name+'".'+' Expected '+expectedTypes.map(capitalize).join(', ')+', got '+Object.prototype.toString.call(value).slice(8,-1)+'.',vm);return;}var validator=prop.validator;if(validator){if(!validator(value)){warn('Invalid prop: custom validator check failed for prop "'+name+'".',vm);}}}/**
 * Assert the type of a value
 */function assertType(value,type){var valid;var expectedType=getType(type);if(expectedType==='String'){valid=(typeof value==='undefined'?'undefined':_typeof(value))===(expectedType='string');}else if(expectedType==='Number'){valid=(typeof value==='undefined'?'undefined':_typeof(value))===(expectedType='number');}else if(expectedType==='Boolean'){valid=(typeof value==='undefined'?'undefined':_typeof(value))===(expectedType='boolean');}else if(expectedType==='Function'){valid=(typeof value==='undefined'?'undefined':_typeof(value))===(expectedType='function');}else if(expectedType==='Object'){valid=isPlainObject(value);}else if(expectedType==='Array'){valid=Array.isArray(value);}else{valid=value instanceof type;}return{valid:valid,expectedType:expectedType};}/**
 * Use function string name to check built-in types,
 * because a simple equality check will fail when running
 * across different vms / iframes.
 */function getType(fn){var match=fn&&fn.toString().match(/^\s*function (\w+)/);return match&&match[1];}function isBooleanType(fn){if(!Array.isArray(fn)){return getType(fn)==='Boolean';}for(var i=0,len=fn.length;i<len;i++){if(getType(fn[i])==='Boolean'){return true;}}/* istanbul ignore next */return false;}var util=Object.freeze({defineReactive:defineReactive$$1,_toString:_toString,toNumber:toNumber,makeMap:makeMap,isBuiltInTag:isBuiltInTag,remove:remove$1,hasOwn:hasOwn,isPrimitive:isPrimitive,cached:cached,camelize:camelize,capitalize:capitalize,hyphenate:hyphenate,bind:bind$1,toArray:toArray,extend:extend,isObject:isObject,isPlainObject:isPlainObject,toObject:toObject,noop:noop,no:no,genStaticKeys:genStaticKeys,looseEqual:looseEqual,looseIndexOf:looseIndexOf,isReserved:isReserved,def:def,parsePath:parsePath,hasProto:hasProto,inBrowser:inBrowser,UA:UA,isIE:isIE,isIE9:isIE9,isEdge:isEdge,isAndroid:isAndroid,isIOS:isIOS,devtools:devtools,nextTick:nextTick,get _Set(){return _Set;},mergeOptions:mergeOptions,resolveAsset:resolveAsset,get warn(){return warn;},get formatComponentName(){return formatComponentName;},validateProp:validateProp});/*  */function initUse(Vue){Vue.use=function(plugin){/* istanbul ignore if */if(plugin.installed){return;}// additional parameters
var args=toArray(arguments,1);args.unshift(this);if(typeof plugin.install==='function'){plugin.install.apply(plugin,args);}else{plugin.apply(null,args);}plugin.installed=true;return this;};}/*  */function initMixin$1(Vue){Vue.mixin=function(mixin){Vue.options=mergeOptions(Vue.options,mixin);};}/*  */function initExtend(Vue){/**
   * Each instance constructor, including Vue, has a unique
   * cid. This enables us to create wrapped "child
   * constructors" for prototypal inheritance and cache them.
   */Vue.cid=0;var cid=1;/**
   * Class inheritance
   */Vue.extend=function(extendOptions){extendOptions=extendOptions||{};var Super=this;var isFirstExtend=Super.cid===0;if(isFirstExtend&&extendOptions._Ctor){return extendOptions._Ctor;}var name=extendOptions.name||Super.options.name;{if(!/^[a-zA-Z][\w-]*$/.test(name)){warn('Invalid component name: "'+name+'". Component names '+'can only contain alphanumeric characaters and the hyphen.');name=null;}}var Sub=function VueComponent(options){this._init(options);};Sub.prototype=Object.create(Super.prototype);Sub.prototype.constructor=Sub;Sub.cid=cid++;Sub.options=mergeOptions(Super.options,extendOptions);Sub['super']=Super;// allow further extension
Sub.extend=Super.extend;// create asset registers, so extended classes
// can have their private assets too.
config._assetTypes.forEach(function(type){Sub[type]=Super[type];});// enable recursive self-lookup
if(name){Sub.options.components[name]=Sub;}// keep a reference to the super options at extension time.
// later at instantiation we can check if Super's options have
// been updated.
Sub.superOptions=Super.options;Sub.extendOptions=extendOptions;// cache constructor
if(isFirstExtend){extendOptions._Ctor=Sub;}return Sub;};}/*  */function initAssetRegisters(Vue){/**
   * Create asset registration methods.
   */config._assetTypes.forEach(function(type){Vue[type]=function(id,definition){if(!definition){return this.options[type+'s'][id];}else{/* istanbul ignore if */{if(type==='component'&&config.isReservedTag(id)){warn('Do not use built-in or reserved HTML elements as component '+'id: '+id);}}if(type==='component'&&isPlainObject(definition)){definition.name=definition.name||id;definition=Vue.extend(definition);}if(type==='directive'&&typeof definition==='function'){definition={bind:definition,update:definition};}this.options[type+'s'][id]=definition;return definition;}};});}var KeepAlive={name:'keep-alive',abstract:true,created:function created(){this.cache=Object.create(null);},render:function render(){var vnode=getFirstComponentChild(this.$slots.default);if(vnode&&vnode.componentOptions){var opts=vnode.componentOptions;var key=vnode.key==null// same constructor may get registered as different local components
// so cid alone is not enough (#3269)
?opts.Ctor.cid+'::'+opts.tag:vnode.key;if(this.cache[key]){vnode.child=this.cache[key].child;}else{this.cache[key]=vnode;}vnode.data.keepAlive=true;}return vnode;},destroyed:function destroyed(){var this$1=this;for(var key in this.cache){var vnode=this$1.cache[key];callHook(vnode.child,'deactivated');vnode.child.$destroy();}}};var builtInComponents={KeepAlive:KeepAlive};/*  */function initGlobalAPI(Vue){// config
var configDef={};configDef.get=function(){return config;};{configDef.set=function(){warn('Do not replace the Vue.config object, set individual fields instead.');};}Object.defineProperty(Vue,'config',configDef);Vue.util=util;Vue.set=set;Vue.delete=del;Vue.nextTick=nextTick;Vue.options=Object.create(null);config._assetTypes.forEach(function(type){Vue.options[type+'s']=Object.create(null);});extend(Vue.options.components,builtInComponents);initUse(Vue);initMixin$1(Vue);initExtend(Vue);initAssetRegisters(Vue);}initGlobalAPI(Vue$3);Object.defineProperty(Vue$3.prototype,'$isServer',{get:function get(){return config._isServer;}});Vue$3.version='2.0.3';/*  */// attributes that should be using props for binding
var mustUseProp=makeMap('value,selected,checked,muted');var isEnumeratedAttr=makeMap('contenteditable,draggable,spellcheck');var isBooleanAttr=makeMap('allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,'+'default,defaultchecked,defaultmuted,defaultselected,defer,disabled,'+'enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,'+'muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,'+'required,reversed,scoped,seamless,selected,sortable,translate,'+'truespeed,typemustmatch,visible');var isAttr=makeMap('accept,accept-charset,accesskey,action,align,alt,async,autocomplete,'+'autofocus,autoplay,autosave,bgcolor,border,buffered,challenge,charset,'+'checked,cite,class,code,codebase,color,cols,colspan,content,http-equiv,'+'name,contenteditable,contextmenu,controls,coords,data,datetime,default,'+'defer,dir,dirname,disabled,download,draggable,dropzone,enctype,method,for,'+'form,formaction,headers,<th>,height,hidden,high,href,hreflang,http-equiv,'+'icon,id,ismap,itemprop,keytype,kind,label,lang,language,list,loop,low,'+'manifest,max,maxlength,media,method,GET,POST,min,multiple,email,file,'+'muted,name,novalidate,open,optimum,pattern,ping,placeholder,poster,'+'preload,radiogroup,readonly,rel,required,reversed,rows,rowspan,sandbox,'+'scope,scoped,seamless,selected,shape,size,type,text,password,sizes,span,'+'spellcheck,src,srcdoc,srclang,srcset,start,step,style,summary,tabindex,'+'target,title,type,usemap,value,width,wrap');var xlinkNS='http://www.w3.org/1999/xlink';var isXlink=function isXlink(name){return name.charAt(5)===':'&&name.slice(0,5)==='xlink';};var getXlinkProp=function getXlinkProp(name){return isXlink(name)?name.slice(6,name.length):'';};var isFalsyAttrValue=function isFalsyAttrValue(val){return val==null||val===false;};/*  */function genClassForVnode(vnode){var data=vnode.data;var parentNode=vnode;var childNode=vnode;while(childNode.child){childNode=childNode.child._vnode;if(childNode.data){data=mergeClassData(childNode.data,data);}}while(parentNode=parentNode.parent){if(parentNode.data){data=mergeClassData(data,parentNode.data);}}return genClassFromData(data);}function mergeClassData(child,parent){return{staticClass:concat(child.staticClass,parent.staticClass),class:child.class?[child.class,parent.class]:parent.class};}function genClassFromData(data){var dynamicClass=data.class;var staticClass=data.staticClass;if(staticClass||dynamicClass){return concat(staticClass,stringifyClass(dynamicClass));}/* istanbul ignore next */return'';}function concat(a,b){return a?b?a+' '+b:a:b||'';}function stringifyClass(value){var res='';if(!value){return res;}if(typeof value==='string'){return value;}if(Array.isArray(value)){var stringified;for(var i=0,l=value.length;i<l;i++){if(value[i]){if(stringified=stringifyClass(value[i])){res+=stringified+' ';}}}return res.slice(0,-1);}if(isObject(value)){for(var key in value){if(value[key]){res+=key+' ';}}return res.slice(0,-1);}/* istanbul ignore next */return res;}/*  */var namespaceMap={svg:'http://www.w3.org/2000/svg',math:'http://www.w3.org/1998/Math/MathML'};var isHTMLTag=makeMap('html,body,base,head,link,meta,style,title,'+'address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,'+'div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,'+'a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,'+'s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,'+'embed,object,param,source,canvas,script,noscript,del,ins,'+'caption,col,colgroup,table,thead,tbody,td,th,tr,'+'button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,'+'output,progress,select,textarea,'+'details,dialog,menu,menuitem,summary,'+'content,element,shadow,template');var isUnaryTag=makeMap('area,base,br,col,embed,frame,hr,img,input,isindex,keygen,'+'link,meta,param,source,track,wbr',true);// Elements that you can, intentionally, leave open
// (and which close themselves)
var canBeLeftOpenTag=makeMap('colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source',true);// HTML5 tags https://html.spec.whatwg.org/multipage/indices.html#elements-3
// Phrasing Content https://html.spec.whatwg.org/multipage/dom.html#phrasing-content
var isNonPhrasingTag=makeMap('address,article,aside,base,blockquote,body,caption,col,colgroup,dd,'+'details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,'+'h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,'+'optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,'+'title,tr,track',true);// this map is intentionally selective, only covering SVG elements that may
// contain child elements.
var isSVG=makeMap('svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font,'+'font-face,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,'+'polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view',true);var isPreTag=function isPreTag(tag){return tag==='pre';};var isReservedTag=function isReservedTag(tag){return isHTMLTag(tag)||isSVG(tag);};function getTagNamespace(tag){if(isSVG(tag)){return'svg';}// basic support for MathML
// note it doesn't support other MathML elements being component roots
if(tag==='math'){return'math';}}var unknownElementCache=Object.create(null);function isUnknownElement(tag){/* istanbul ignore if */if(!inBrowser){return true;}if(isReservedTag(tag)){return false;}tag=tag.toLowerCase();/* istanbul ignore if */if(unknownElementCache[tag]!=null){return unknownElementCache[tag];}var el=document.createElement(tag);if(tag.indexOf('-')>-1){// http://stackoverflow.com/a/28210364/1070244
return unknownElementCache[tag]=el.constructor===window.HTMLUnknownElement||el.constructor===window.HTMLElement;}else{return unknownElementCache[tag]=/HTMLUnknownElement/.test(el.toString());}}/*  *//**
 * Query an element selector if it's not an element already.
 */function query(el){if(typeof el==='string'){var selector=el;el=document.querySelector(el);if(!el){"development"!=='production'&&warn('Cannot find element: '+selector);return document.createElement('div');}}return el;}/*  */function createElement$1(tagName,vnode){var elm=document.createElement(tagName);if(tagName!=='select'){return elm;}if(vnode.data&&vnode.data.attrs&&'multiple'in vnode.data.attrs){elm.setAttribute('multiple','multiple');}return elm;}function createElementNS(namespace,tagName){return document.createElementNS(namespaceMap[namespace],tagName);}function createTextNode(text){return document.createTextNode(text);}function createComment(text){return document.createComment(text);}function insertBefore(parentNode,newNode,referenceNode){parentNode.insertBefore(newNode,referenceNode);}function removeChild(node,child){node.removeChild(child);}function appendChild(node,child){node.appendChild(child);}function parentNode(node){return node.parentNode;}function nextSibling(node){return node.nextSibling;}function tagName(node){return node.tagName;}function setTextContent(node,text){node.textContent=text;}function childNodes(node){return node.childNodes;}function setAttribute(node,key,val){node.setAttribute(key,val);}var nodeOps=Object.freeze({createElement:createElement$1,createElementNS:createElementNS,createTextNode:createTextNode,createComment:createComment,insertBefore:insertBefore,removeChild:removeChild,appendChild:appendChild,parentNode:parentNode,nextSibling:nextSibling,tagName:tagName,setTextContent:setTextContent,childNodes:childNodes,setAttribute:setAttribute});/*  */var ref={create:function create(_,vnode){registerRef(vnode);},update:function update(oldVnode,vnode){if(oldVnode.data.ref!==vnode.data.ref){registerRef(oldVnode,true);registerRef(vnode);}},destroy:function destroy(vnode){registerRef(vnode,true);}};function registerRef(vnode,isRemoval){var key=vnode.data.ref;if(!key){return;}var vm=vnode.context;var ref=vnode.child||vnode.elm;var refs=vm.$refs;if(isRemoval){if(Array.isArray(refs[key])){remove$1(refs[key],ref);}else if(refs[key]===ref){refs[key]=undefined;}}else{if(vnode.data.refInFor){if(Array.isArray(refs[key])){refs[key].push(ref);}else{refs[key]=[ref];}}else{refs[key]=ref;}}}/**
 * Virtual DOM patching algorithm based on Snabbdom by
 * Simon Friis Vindum (@paldepind)
 * Licensed under the MIT License
 * https://github.com/paldepind/snabbdom/blob/master/LICENSE
 *
 * modified by Evan You (@yyx990803)
 *

/*
 * Not type-checking this because this file is perf-critical and the cost
 * of making flow understand it is not worth it.
 */var emptyNode=new VNode('',{},[]);var hooks$1=['create','update','remove','destroy'];function isUndef(s){return s==null;}function isDef(s){return s!=null;}function sameVnode(vnode1,vnode2){return vnode1.key===vnode2.key&&vnode1.tag===vnode2.tag&&vnode1.isComment===vnode2.isComment&&!vnode1.data===!vnode2.data;}function createKeyToOldIdx(children,beginIdx,endIdx){var i,key;var map={};for(i=beginIdx;i<=endIdx;++i){key=children[i].key;if(isDef(key)){map[key]=i;}}return map;}function createPatchFunction(backend){var i,j;var cbs={};var modules=backend.modules;var nodeOps=backend.nodeOps;for(i=0;i<hooks$1.length;++i){cbs[hooks$1[i]]=[];for(j=0;j<modules.length;++j){if(modules[j][hooks$1[i]]!==undefined){cbs[hooks$1[i]].push(modules[j][hooks$1[i]]);}}}function emptyNodeAt(elm){return new VNode(nodeOps.tagName(elm).toLowerCase(),{},[],undefined,elm);}function createRmCb(childElm,listeners){function remove$$1(){if(--remove$$1.listeners===0){removeElement(childElm);}}remove$$1.listeners=listeners;return remove$$1;}function removeElement(el){var parent=nodeOps.parentNode(el);nodeOps.removeChild(parent,el);}function createElm(vnode,insertedVnodeQueue,nested){var i;var data=vnode.data;vnode.isRootInsert=!nested;if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.init)){i(vnode);}// after calling the init hook, if the vnode is a child component
// it should've created a child instance and mounted it. the child
// component also has set the placeholder vnode's elm.
// in that case we can just return the element and be done.
if(isDef(i=vnode.child)){initComponent(vnode,insertedVnodeQueue);return vnode.elm;}}var children=vnode.children;var tag=vnode.tag;if(isDef(tag)){{if(!vnode.ns&&!(config.ignoredElements&&config.ignoredElements.indexOf(tag)>-1)&&config.isUnknownElement(tag)){warn('Unknown custom element: <'+tag+'> - did you '+'register the component correctly? For recursive components, '+'make sure to provide the "name" option.',vnode.context);}}vnode.elm=vnode.ns?nodeOps.createElementNS(vnode.ns,tag):nodeOps.createElement(tag,vnode);setScope(vnode);createChildren(vnode,children,insertedVnodeQueue);if(isDef(data)){invokeCreateHooks(vnode,insertedVnodeQueue);}}else if(vnode.isComment){vnode.elm=nodeOps.createComment(vnode.text);}else{vnode.elm=nodeOps.createTextNode(vnode.text);}return vnode.elm;}function createChildren(vnode,children,insertedVnodeQueue){if(Array.isArray(children)){for(var i=0;i<children.length;++i){nodeOps.appendChild(vnode.elm,createElm(children[i],insertedVnodeQueue,true));}}else if(isPrimitive(vnode.text)){nodeOps.appendChild(vnode.elm,nodeOps.createTextNode(vnode.text));}}function isPatchable(vnode){while(vnode.child){vnode=vnode.child._vnode;}return isDef(vnode.tag);}function invokeCreateHooks(vnode,insertedVnodeQueue){for(var i$1=0;i$1<cbs.create.length;++i$1){cbs.create[i$1](emptyNode,vnode);}i=vnode.data.hook;// Reuse variable
if(isDef(i)){if(i.create){i.create(emptyNode,vnode);}if(i.insert){insertedVnodeQueue.push(vnode);}}}function initComponent(vnode,insertedVnodeQueue){if(vnode.data.pendingInsert){insertedVnodeQueue.push.apply(insertedVnodeQueue,vnode.data.pendingInsert);}vnode.elm=vnode.child.$el;if(isPatchable(vnode)){invokeCreateHooks(vnode,insertedVnodeQueue);setScope(vnode);}else{// empty component root.
// skip all element-related modules except for ref (#3455)
registerRef(vnode);// make sure to invoke the insert hook
insertedVnodeQueue.push(vnode);}}// set scope id attribute for scoped CSS.
// this is implemented as a special case to avoid the overhead
// of going through the normal attribute patching process.
function setScope(vnode){var i;if(isDef(i=vnode.context)&&isDef(i=i.$options._scopeId)){nodeOps.setAttribute(vnode.elm,i,'');}if(isDef(i=activeInstance)&&i!==vnode.context&&isDef(i=i.$options._scopeId)){nodeOps.setAttribute(vnode.elm,i,'');}}function addVnodes(parentElm,before,vnodes,startIdx,endIdx,insertedVnodeQueue){for(;startIdx<=endIdx;++startIdx){nodeOps.insertBefore(parentElm,createElm(vnodes[startIdx],insertedVnodeQueue),before);}}function invokeDestroyHook(vnode){var i,j;var data=vnode.data;if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.destroy)){i(vnode);}for(i=0;i<cbs.destroy.length;++i){cbs.destroy[i](vnode);}}if(isDef(i=vnode.children)){for(j=0;j<vnode.children.length;++j){invokeDestroyHook(vnode.children[j]);}}}function removeVnodes(parentElm,vnodes,startIdx,endIdx){for(;startIdx<=endIdx;++startIdx){var ch=vnodes[startIdx];if(isDef(ch)){if(isDef(ch.tag)){removeAndInvokeRemoveHook(ch);invokeDestroyHook(ch);}else{// Text node
nodeOps.removeChild(parentElm,ch.elm);}}}}function removeAndInvokeRemoveHook(vnode,rm){if(rm||isDef(vnode.data)){var listeners=cbs.remove.length+1;if(!rm){// directly removing
rm=createRmCb(vnode.elm,listeners);}else{// we have a recursively passed down rm callback
// increase the listeners count
rm.listeners+=listeners;}// recursively invoke hooks on child component root node
if(isDef(i=vnode.child)&&isDef(i=i._vnode)&&isDef(i.data)){removeAndInvokeRemoveHook(i,rm);}for(i=0;i<cbs.remove.length;++i){cbs.remove[i](vnode,rm);}if(isDef(i=vnode.data.hook)&&isDef(i=i.remove)){i(vnode,rm);}else{rm();}}else{removeElement(vnode.elm);}}function updateChildren(parentElm,oldCh,newCh,insertedVnodeQueue,removeOnly){var oldStartIdx=0;var newStartIdx=0;var oldEndIdx=oldCh.length-1;var oldStartVnode=oldCh[0];var oldEndVnode=oldCh[oldEndIdx];var newEndIdx=newCh.length-1;var newStartVnode=newCh[0];var newEndVnode=newCh[newEndIdx];var oldKeyToIdx,idxInOld,elmToMove,before;// removeOnly is a special flag used only by <transition-group>
// to ensure removed elements stay in correct relative positions
// during leaving transitions
var canMove=!removeOnly;while(oldStartIdx<=oldEndIdx&&newStartIdx<=newEndIdx){if(isUndef(oldStartVnode)){oldStartVnode=oldCh[++oldStartIdx];// Vnode has been moved left
}else if(isUndef(oldEndVnode)){oldEndVnode=oldCh[--oldEndIdx];}else if(sameVnode(oldStartVnode,newStartVnode)){patchVnode(oldStartVnode,newStartVnode,insertedVnodeQueue);oldStartVnode=oldCh[++oldStartIdx];newStartVnode=newCh[++newStartIdx];}else if(sameVnode(oldEndVnode,newEndVnode)){patchVnode(oldEndVnode,newEndVnode,insertedVnodeQueue);oldEndVnode=oldCh[--oldEndIdx];newEndVnode=newCh[--newEndIdx];}else if(sameVnode(oldStartVnode,newEndVnode)){// Vnode moved right
patchVnode(oldStartVnode,newEndVnode,insertedVnodeQueue);canMove&&nodeOps.insertBefore(parentElm,oldStartVnode.elm,nodeOps.nextSibling(oldEndVnode.elm));oldStartVnode=oldCh[++oldStartIdx];newEndVnode=newCh[--newEndIdx];}else if(sameVnode(oldEndVnode,newStartVnode)){// Vnode moved left
patchVnode(oldEndVnode,newStartVnode,insertedVnodeQueue);canMove&&nodeOps.insertBefore(parentElm,oldEndVnode.elm,oldStartVnode.elm);oldEndVnode=oldCh[--oldEndIdx];newStartVnode=newCh[++newStartIdx];}else{if(isUndef(oldKeyToIdx)){oldKeyToIdx=createKeyToOldIdx(oldCh,oldStartIdx,oldEndIdx);}idxInOld=isDef(newStartVnode.key)?oldKeyToIdx[newStartVnode.key]:null;if(isUndef(idxInOld)){// New element
nodeOps.insertBefore(parentElm,createElm(newStartVnode,insertedVnodeQueue),oldStartVnode.elm);newStartVnode=newCh[++newStartIdx];}else{elmToMove=oldCh[idxInOld];/* istanbul ignore if */if("development"!=='production'&&!elmToMove){warn('It seems there are duplicate keys that is causing an update error. '+'Make sure each v-for item has a unique key.');}if(elmToMove.tag!==newStartVnode.tag){// same key but different element. treat as new element
nodeOps.insertBefore(parentElm,createElm(newStartVnode,insertedVnodeQueue),oldStartVnode.elm);newStartVnode=newCh[++newStartIdx];}else{patchVnode(elmToMove,newStartVnode,insertedVnodeQueue);oldCh[idxInOld]=undefined;canMove&&nodeOps.insertBefore(parentElm,newStartVnode.elm,oldStartVnode.elm);newStartVnode=newCh[++newStartIdx];}}}}if(oldStartIdx>oldEndIdx){before=isUndef(newCh[newEndIdx+1])?null:newCh[newEndIdx+1].elm;addVnodes(parentElm,before,newCh,newStartIdx,newEndIdx,insertedVnodeQueue);}else if(newStartIdx>newEndIdx){removeVnodes(parentElm,oldCh,oldStartIdx,oldEndIdx);}}function patchVnode(oldVnode,vnode,insertedVnodeQueue,removeOnly){if(oldVnode===vnode){return;}// reuse element for static trees.
// note we only do this if the vnode is cloned -
// if the new node is not cloned it means the render functions have been
// reset by the hot-reload-api and we need to do a proper re-render.
if(vnode.isStatic&&oldVnode.isStatic&&vnode.key===oldVnode.key&&vnode.isCloned){vnode.elm=oldVnode.elm;return;}var i;var data=vnode.data;var hasData=isDef(data);if(hasData&&isDef(i=data.hook)&&isDef(i=i.prepatch)){i(oldVnode,vnode);}var elm=vnode.elm=oldVnode.elm;var oldCh=oldVnode.children;var ch=vnode.children;if(hasData&&isPatchable(vnode)){for(i=0;i<cbs.update.length;++i){cbs.update[i](oldVnode,vnode);}if(isDef(i=data.hook)&&isDef(i=i.update)){i(oldVnode,vnode);}}if(isUndef(vnode.text)){if(isDef(oldCh)&&isDef(ch)){if(oldCh!==ch){updateChildren(elm,oldCh,ch,insertedVnodeQueue,removeOnly);}}else if(isDef(ch)){if(isDef(oldVnode.text)){nodeOps.setTextContent(elm,'');}addVnodes(elm,null,ch,0,ch.length-1,insertedVnodeQueue);}else if(isDef(oldCh)){removeVnodes(elm,oldCh,0,oldCh.length-1);}else if(isDef(oldVnode.text)){nodeOps.setTextContent(elm,'');}}else if(oldVnode.text!==vnode.text){nodeOps.setTextContent(elm,vnode.text);}if(hasData){if(isDef(i=data.hook)&&isDef(i=i.postpatch)){i(oldVnode,vnode);}}}function invokeInsertHook(vnode,queue,initial){// delay insert hooks for component root nodes, invoke them after the
// element is really inserted
if(initial&&vnode.parent){vnode.parent.data.pendingInsert=queue;}else{for(var i=0;i<queue.length;++i){queue[i].data.hook.insert(queue[i]);}}}var bailed=false;function hydrate(elm,vnode,insertedVnodeQueue){{if(!assertNodeMatch(elm,vnode)){return false;}}vnode.elm=elm;var tag=vnode.tag;var data=vnode.data;var children=vnode.children;if(isDef(data)){if(isDef(i=data.hook)&&isDef(i=i.init)){i(vnode,true/* hydrating */);}if(isDef(i=vnode.child)){// child component. it should have hydrated its own tree.
initComponent(vnode,insertedVnodeQueue);return true;}}if(isDef(tag)){if(isDef(children)){var childNodes=nodeOps.childNodes(elm);// empty element, allow client to pick up and populate children
if(!childNodes.length){createChildren(vnode,children,insertedVnodeQueue);}else{var childrenMatch=true;if(childNodes.length!==children.length){childrenMatch=false;}else{for(var i$1=0;i$1<children.length;i$1++){if(!hydrate(childNodes[i$1],children[i$1],insertedVnodeQueue)){childrenMatch=false;break;}}}if(!childrenMatch){if("development"!=='production'&&typeof console!=='undefined'&&!bailed){bailed=true;console.warn('Parent: ',elm);console.warn('Mismatching childNodes vs. VNodes: ',childNodes,children);}return false;}}}if(isDef(data)){invokeCreateHooks(vnode,insertedVnodeQueue);}}return true;}function assertNodeMatch(node,vnode){if(vnode.tag){return vnode.tag.indexOf('vue-component')===0||vnode.tag===nodeOps.tagName(node).toLowerCase();}else{return _toString(vnode.text)===node.data;}}return function patch(oldVnode,vnode,hydrating,removeOnly){if(!vnode){if(oldVnode){invokeDestroyHook(oldVnode);}return;}var elm,parent;var isInitialPatch=false;var insertedVnodeQueue=[];if(!oldVnode){// empty mount, create new root element
isInitialPatch=true;createElm(vnode,insertedVnodeQueue);}else{var isRealElement=isDef(oldVnode.nodeType);if(!isRealElement&&sameVnode(oldVnode,vnode)){patchVnode(oldVnode,vnode,insertedVnodeQueue,removeOnly);}else{if(isRealElement){// mounting to a real element
// check if this is server-rendered content and if we can perform
// a successful hydration.
if(oldVnode.nodeType===1&&oldVnode.hasAttribute('server-rendered')){oldVnode.removeAttribute('server-rendered');hydrating=true;}if(hydrating){if(hydrate(oldVnode,vnode,insertedVnodeQueue)){invokeInsertHook(vnode,insertedVnodeQueue,true);return oldVnode;}else{warn('The client-side rendered virtual DOM tree is not matching '+'server-rendered content. This is likely caused by incorrect '+'HTML markup, for example nesting block-level elements inside '+'<p>, or missing <tbody>. Bailing hydration and performing '+'full client-side render.');}}// either not server-rendered, or hydration failed.
// create an empty node and replace it
oldVnode=emptyNodeAt(oldVnode);}elm=oldVnode.elm;parent=nodeOps.parentNode(elm);createElm(vnode,insertedVnodeQueue);// component root element replaced.
// update parent placeholder node element.
if(vnode.parent){vnode.parent.elm=vnode.elm;if(isPatchable(vnode)){for(var i=0;i<cbs.create.length;++i){cbs.create[i](emptyNode,vnode.parent);}}}if(parent!==null){nodeOps.insertBefore(parent,vnode.elm,nodeOps.nextSibling(elm));removeVnodes(parent,[oldVnode],0,0);}else if(isDef(oldVnode.tag)){invokeDestroyHook(oldVnode);}}}invokeInsertHook(vnode,insertedVnodeQueue,isInitialPatch);return vnode.elm;};}/*  */var directives={create:updateDirectives,update:updateDirectives,destroy:function unbindDirectives(vnode){updateDirectives(vnode,emptyNode);}};function updateDirectives(oldVnode,vnode){if(!oldVnode.data.directives&&!vnode.data.directives){return;}var isCreate=oldVnode===emptyNode;var oldDirs=normalizeDirectives$1(oldVnode.data.directives,oldVnode.context);var newDirs=normalizeDirectives$1(vnode.data.directives,vnode.context);var dirsWithInsert=[];var dirsWithPostpatch=[];var key,oldDir,dir;for(key in newDirs){oldDir=oldDirs[key];dir=newDirs[key];if(!oldDir){// new directive, bind
callHook$1(dir,'bind',vnode,oldVnode);if(dir.def&&dir.def.inserted){dirsWithInsert.push(dir);}}else{// existing directive, update
dir.oldValue=oldDir.value;callHook$1(dir,'update',vnode,oldVnode);if(dir.def&&dir.def.componentUpdated){dirsWithPostpatch.push(dir);}}}if(dirsWithInsert.length){var callInsert=function callInsert(){dirsWithInsert.forEach(function(dir){callHook$1(dir,'inserted',vnode,oldVnode);});};if(isCreate){mergeVNodeHook(vnode.data.hook||(vnode.data.hook={}),'insert',callInsert,'dir-insert');}else{callInsert();}}if(dirsWithPostpatch.length){mergeVNodeHook(vnode.data.hook||(vnode.data.hook={}),'postpatch',function(){dirsWithPostpatch.forEach(function(dir){callHook$1(dir,'componentUpdated',vnode,oldVnode);});},'dir-postpatch');}if(!isCreate){for(key in oldDirs){if(!newDirs[key]){// no longer present, unbind
callHook$1(oldDirs[key],'unbind',oldVnode);}}}}var emptyModifiers=Object.create(null);function normalizeDirectives$1(dirs,vm){var res=Object.create(null);if(!dirs){return res;}var i,dir;for(i=0;i<dirs.length;i++){dir=dirs[i];if(!dir.modifiers){dir.modifiers=emptyModifiers;}res[getRawDirName(dir)]=dir;dir.def=resolveAsset(vm.$options,'directives',dir.name,true);}return res;}function getRawDirName(dir){return dir.rawName||dir.name+"."+Object.keys(dir.modifiers||{}).join('.');}function callHook$1(dir,hook,vnode,oldVnode){var fn=dir.def&&dir.def[hook];if(fn){fn(vnode.elm,dir,vnode,oldVnode);}}var baseModules=[ref,directives];/*  */function updateAttrs(oldVnode,vnode){if(!oldVnode.data.attrs&&!vnode.data.attrs){return;}var key,cur,old;var elm=vnode.elm;var oldAttrs=oldVnode.data.attrs||{};var attrs=vnode.data.attrs||{};// clone observed objects, as the user probably wants to mutate it
if(attrs.__ob__){attrs=vnode.data.attrs=extend({},attrs);}for(key in attrs){cur=attrs[key];old=oldAttrs[key];if(old!==cur){setAttr(elm,key,cur);}}for(key in oldAttrs){if(attrs[key]==null){if(isXlink(key)){elm.removeAttributeNS(xlinkNS,getXlinkProp(key));}else if(!isEnumeratedAttr(key)){elm.removeAttribute(key);}}}}function setAttr(el,key,value){if(isBooleanAttr(key)){// set attribute for blank value
// e.g. <option disabled>Select one</option>
if(isFalsyAttrValue(value)){el.removeAttribute(key);}else{el.setAttribute(key,key);}}else if(isEnumeratedAttr(key)){el.setAttribute(key,isFalsyAttrValue(value)||value==='false'?'false':'true');}else if(isXlink(key)){if(isFalsyAttrValue(value)){el.removeAttributeNS(xlinkNS,getXlinkProp(key));}else{el.setAttributeNS(xlinkNS,key,value);}}else{if(isFalsyAttrValue(value)){el.removeAttribute(key);}else{el.setAttribute(key,value);}}}var attrs={create:updateAttrs,update:updateAttrs};/*  */function updateClass(oldVnode,vnode){var el=vnode.elm;var data=vnode.data;var oldData=oldVnode.data;if(!data.staticClass&&!data.class&&(!oldData||!oldData.staticClass&&!oldData.class)){return;}var cls=genClassForVnode(vnode);// handle transition classes
var transitionClass=el._transitionClasses;if(transitionClass){cls=concat(cls,stringifyClass(transitionClass));}// set the class
if(cls!==el._prevClass){el.setAttribute('class',cls);el._prevClass=cls;}}var klass={create:updateClass,update:updateClass};// skip type checking this file because we need to attach private properties
// to elements
function updateDOMListeners(oldVnode,vnode){if(!oldVnode.data.on&&!vnode.data.on){return;}var on=vnode.data.on||{};var oldOn=oldVnode.data.on||{};var add=vnode.elm._v_add||(vnode.elm._v_add=function(event,handler,capture){vnode.elm.addEventListener(event,handler,capture);});var remove=vnode.elm._v_remove||(vnode.elm._v_remove=function(event,handler){vnode.elm.removeEventListener(event,handler);});updateListeners(on,oldOn,add,remove,vnode.context);}var events={create:updateDOMListeners,update:updateDOMListeners};/*  */function updateDOMProps(oldVnode,vnode){if(!oldVnode.data.domProps&&!vnode.data.domProps){return;}var key,cur;var elm=vnode.elm;var oldProps=oldVnode.data.domProps||{};var props=vnode.data.domProps||{};// clone observed objects, as the user probably wants to mutate it
if(props.__ob__){props=vnode.data.domProps=extend({},props);}for(key in oldProps){if(props[key]==null){elm[key]=undefined;}}for(key in props){// ignore children if the node has textContent or innerHTML,
// as these will throw away existing DOM nodes and cause removal errors
// on subsequent patches (#3360)
if((key==='textContent'||key==='innerHTML')&&vnode.children){vnode.children.length=0;}cur=props[key];if(key==='value'){// store value as _value as well since
// non-string values will be stringified
elm._value=cur;// avoid resetting cursor position when value is the same
var strCur=cur==null?'':String(cur);if(elm.value!==strCur&&!elm.composing){elm.value=strCur;}}else{elm[key]=cur;}}}var domProps={create:updateDOMProps,update:updateDOMProps};/*  */var prefixes=['Webkit','Moz','ms'];var testEl;var normalize=cached(function(prop){testEl=testEl||document.createElement('div');prop=camelize(prop);if(prop!=='filter'&&prop in testEl.style){return prop;}var upper=prop.charAt(0).toUpperCase()+prop.slice(1);for(var i=0;i<prefixes.length;i++){var prefixed=prefixes[i]+upper;if(prefixed in testEl.style){return prefixed;}}});function updateStyle(oldVnode,vnode){if((!oldVnode.data||!oldVnode.data.style)&&!vnode.data.style){return;}var cur,name;var el=vnode.elm;var oldStyle=oldVnode.data.style||{};var style=vnode.data.style||{};// handle string
if(typeof style==='string'){el.style.cssText=style;return;}var needClone=style.__ob__;// handle array syntax
if(Array.isArray(style)){style=vnode.data.style=toObject(style);}// clone the style for future updates,
// in case the user mutates the style object in-place.
if(needClone){style=vnode.data.style=extend({},style);}for(name in oldStyle){if(style[name]==null){el.style[normalize(name)]='';}}for(name in style){cur=style[name];if(cur!==oldStyle[name]){// ie9 setting to null has no effect, must use empty string
el.style[normalize(name)]=cur==null?'':cur;}}}var style={create:updateStyle,update:updateStyle};/*  *//**
 * Add class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */function addClass(el,cls){/* istanbul ignore else */if(el.classList){if(cls.indexOf(' ')>-1){cls.split(/\s+/).forEach(function(c){return el.classList.add(c);});}else{el.classList.add(cls);}}else{var cur=' '+el.getAttribute('class')+' ';if(cur.indexOf(' '+cls+' ')<0){el.setAttribute('class',(cur+cls).trim());}}}/**
 * Remove class with compatibility for SVG since classList is not supported on
 * SVG elements in IE
 */function removeClass(el,cls){/* istanbul ignore else */if(el.classList){if(cls.indexOf(' ')>-1){cls.split(/\s+/).forEach(function(c){return el.classList.remove(c);});}else{el.classList.remove(cls);}}else{var cur=' '+el.getAttribute('class')+' ';var tar=' '+cls+' ';while(cur.indexOf(tar)>=0){cur=cur.replace(tar,' ');}el.setAttribute('class',cur.trim());}}/*  */var hasTransition=inBrowser&&!isIE9;var TRANSITION='transition';var ANIMATION='animation';// Transition property/event sniffing
var transitionProp='transition';var transitionEndEvent='transitionend';var animationProp='animation';var animationEndEvent='animationend';if(hasTransition){/* istanbul ignore if */if(window.ontransitionend===undefined&&window.onwebkittransitionend!==undefined){transitionProp='WebkitTransition';transitionEndEvent='webkitTransitionEnd';}if(window.onanimationend===undefined&&window.onwebkitanimationend!==undefined){animationProp='WebkitAnimation';animationEndEvent='webkitAnimationEnd';}}var raf=inBrowser&&window.requestAnimationFrame||setTimeout;function nextFrame(fn){raf(function(){raf(fn);});}function addTransitionClass(el,cls){(el._transitionClasses||(el._transitionClasses=[])).push(cls);addClass(el,cls);}function removeTransitionClass(el,cls){if(el._transitionClasses){remove$1(el._transitionClasses,cls);}removeClass(el,cls);}function whenTransitionEnds(el,expectedType,cb){var ref=getTransitionInfo(el,expectedType);var type=ref.type;var timeout=ref.timeout;var propCount=ref.propCount;if(!type){return cb();}var event=type===TRANSITION?transitionEndEvent:animationEndEvent;var ended=0;var end=function end(){el.removeEventListener(event,onEnd);cb();};var onEnd=function onEnd(e){if(e.target===el){if(++ended>=propCount){end();}}};setTimeout(function(){if(ended<propCount){end();}},timeout+1);el.addEventListener(event,onEnd);}var transformRE=/\b(transform|all)(,|$)/;function getTransitionInfo(el,expectedType){var styles=window.getComputedStyle(el);var transitioneDelays=styles[transitionProp+'Delay'].split(', ');var transitionDurations=styles[transitionProp+'Duration'].split(', ');var transitionTimeout=getTimeout(transitioneDelays,transitionDurations);var animationDelays=styles[animationProp+'Delay'].split(', ');var animationDurations=styles[animationProp+'Duration'].split(', ');var animationTimeout=getTimeout(animationDelays,animationDurations);var type;var timeout=0;var propCount=0;/* istanbul ignore if */if(expectedType===TRANSITION){if(transitionTimeout>0){type=TRANSITION;timeout=transitionTimeout;propCount=transitionDurations.length;}}else if(expectedType===ANIMATION){if(animationTimeout>0){type=ANIMATION;timeout=animationTimeout;propCount=animationDurations.length;}}else{timeout=Math.max(transitionTimeout,animationTimeout);type=timeout>0?transitionTimeout>animationTimeout?TRANSITION:ANIMATION:null;propCount=type?type===TRANSITION?transitionDurations.length:animationDurations.length:0;}var hasTransform=type===TRANSITION&&transformRE.test(styles[transitionProp+'Property']);return{type:type,timeout:timeout,propCount:propCount,hasTransform:hasTransform};}function getTimeout(delays,durations){return Math.max.apply(null,durations.map(function(d,i){return toMs(d)+toMs(delays[i]);}));}function toMs(s){return Number(s.slice(0,-1))*1000;}/*  */function enter(vnode){var el=vnode.elm;// call leave callback now
if(el._leaveCb){el._leaveCb.cancelled=true;el._leaveCb();}var data=resolveTransition(vnode.data.transition);if(!data){return;}/* istanbul ignore if */if(el._enterCb||el.nodeType!==1){return;}var css=data.css;var type=data.type;var enterClass=data.enterClass;var enterActiveClass=data.enterActiveClass;var appearClass=data.appearClass;var appearActiveClass=data.appearActiveClass;var beforeEnter=data.beforeEnter;var enter=data.enter;var afterEnter=data.afterEnter;var enterCancelled=data.enterCancelled;var beforeAppear=data.beforeAppear;var appear=data.appear;var afterAppear=data.afterAppear;var appearCancelled=data.appearCancelled;// activeInstance will always be the <transition> component managing this
// transition. One edge case to check is when the <transition> is placed
// as the root node of a child component. In that case we need to check
// <transition>'s parent for appear check.
var transitionNode=activeInstance.$vnode;var context=transitionNode&&transitionNode.parent?transitionNode.parent.context:activeInstance;var isAppear=!context._isMounted||!vnode.isRootInsert;if(isAppear&&!appear&&appear!==''){return;}var startClass=isAppear?appearClass:enterClass;var activeClass=isAppear?appearActiveClass:enterActiveClass;var beforeEnterHook=isAppear?beforeAppear||beforeEnter:beforeEnter;var enterHook=isAppear?typeof appear==='function'?appear:enter:enter;var afterEnterHook=isAppear?afterAppear||afterEnter:afterEnter;var enterCancelledHook=isAppear?appearCancelled||enterCancelled:enterCancelled;var expectsCSS=css!==false&&!isIE9;var userWantsControl=enterHook&&// enterHook may be a bound method which exposes
// the length of original fn as _length
(enterHook._length||enterHook.length)>1;var cb=el._enterCb=once(function(){if(expectsCSS){removeTransitionClass(el,activeClass);}if(cb.cancelled){if(expectsCSS){removeTransitionClass(el,startClass);}enterCancelledHook&&enterCancelledHook(el);}else{afterEnterHook&&afterEnterHook(el);}el._enterCb=null;});if(!vnode.data.show){// remove pending leave element on enter by injecting an insert hook
mergeVNodeHook(vnode.data.hook||(vnode.data.hook={}),'insert',function(){var parent=el.parentNode;var pendingNode=parent&&parent._pending&&parent._pending[vnode.key];if(pendingNode&&pendingNode.tag===vnode.tag&&pendingNode.elm._leaveCb){pendingNode.elm._leaveCb();}enterHook&&enterHook(el,cb);},'transition-insert');}// start enter transition
beforeEnterHook&&beforeEnterHook(el);if(expectsCSS){addTransitionClass(el,startClass);addTransitionClass(el,activeClass);nextFrame(function(){removeTransitionClass(el,startClass);if(!cb.cancelled&&!userWantsControl){whenTransitionEnds(el,type,cb);}});}if(vnode.data.show){enterHook&&enterHook(el,cb);}if(!expectsCSS&&!userWantsControl){cb();}}function leave(vnode,rm){var el=vnode.elm;// call enter callback now
if(el._enterCb){el._enterCb.cancelled=true;el._enterCb();}var data=resolveTransition(vnode.data.transition);if(!data){return rm();}/* istanbul ignore if */if(el._leaveCb||el.nodeType!==1){return;}var css=data.css;var type=data.type;var leaveClass=data.leaveClass;var leaveActiveClass=data.leaveActiveClass;var beforeLeave=data.beforeLeave;var leave=data.leave;var afterLeave=data.afterLeave;var leaveCancelled=data.leaveCancelled;var delayLeave=data.delayLeave;var expectsCSS=css!==false&&!isIE9;var userWantsControl=leave&&// leave hook may be a bound method which exposes
// the length of original fn as _length
(leave._length||leave.length)>1;var cb=el._leaveCb=once(function(){if(el.parentNode&&el.parentNode._pending){el.parentNode._pending[vnode.key]=null;}if(expectsCSS){removeTransitionClass(el,leaveActiveClass);}if(cb.cancelled){if(expectsCSS){removeTransitionClass(el,leaveClass);}leaveCancelled&&leaveCancelled(el);}else{rm();afterLeave&&afterLeave(el);}el._leaveCb=null;});if(delayLeave){delayLeave(performLeave);}else{performLeave();}function performLeave(){// the delayed leave may have already been cancelled
if(cb.cancelled){return;}// record leaving element
if(!vnode.data.show){(el.parentNode._pending||(el.parentNode._pending={}))[vnode.key]=vnode;}beforeLeave&&beforeLeave(el);if(expectsCSS){addTransitionClass(el,leaveClass);addTransitionClass(el,leaveActiveClass);nextFrame(function(){removeTransitionClass(el,leaveClass);if(!cb.cancelled&&!userWantsControl){whenTransitionEnds(el,type,cb);}});}leave&&leave(el,cb);if(!expectsCSS&&!userWantsControl){cb();}}}function resolveTransition(def$$1){if(!def$$1){return;}/* istanbul ignore else */if((typeof def$$1==='undefined'?'undefined':_typeof(def$$1))==='object'){var res={};if(def$$1.css!==false){extend(res,autoCssTransition(def$$1.name||'v'));}extend(res,def$$1);return res;}else if(typeof def$$1==='string'){return autoCssTransition(def$$1);}}var autoCssTransition=cached(function(name){return{enterClass:name+"-enter",leaveClass:name+"-leave",appearClass:name+"-enter",enterActiveClass:name+"-enter-active",leaveActiveClass:name+"-leave-active",appearActiveClass:name+"-enter-active"};});function once(fn){var called=false;return function(){if(!called){called=true;fn();}};}var transition=inBrowser?{create:function create(_,vnode){if(!vnode.data.show){enter(vnode);}},remove:function remove(vnode,rm){/* istanbul ignore else */if(!vnode.data.show){leave(vnode,rm);}else{rm();}}}:{};var platformModules=[attrs,klass,events,domProps,style,transition];/*  */// the directive module should be applied last, after all
// built-in modules have been applied.
var modules=platformModules.concat(baseModules);var patch$1=createPatchFunction({nodeOps:nodeOps,modules:modules});/**
 * Not type checking this file because flow doesn't like attaching
 * properties to Elements.
 */var modelableTagRE=/^input|select|textarea|vue-component-[0-9]+(-[0-9a-zA-Z_\-]*)?$/;/* istanbul ignore if */if(isIE9){// http://www.matts411.com/post/internet-explorer-9-oninput/
document.addEventListener('selectionchange',function(){var el=document.activeElement;if(el&&el.vmodel){trigger(el,'input');}});}var model={inserted:function inserted(el,binding,vnode){{if(!modelableTagRE.test(vnode.tag)){warn("v-model is not supported on element type: <"+vnode.tag+">. "+'If you are working with contenteditable, it\'s recommended to '+'wrap a library dedicated for that purpose inside a custom component.',vnode.context);}}if(vnode.tag==='select'){var cb=function cb(){setSelected(el,binding,vnode.context);};cb();/* istanbul ignore if */if(isIE||isEdge){setTimeout(cb,0);}}else if((vnode.tag==='textarea'||el.type==='text')&&!binding.modifiers.lazy){if(!isAndroid){el.addEventListener('compositionstart',onCompositionStart);el.addEventListener('compositionend',onCompositionEnd);}/* istanbul ignore if */if(isIE9){el.vmodel=true;}}},componentUpdated:function componentUpdated(el,binding,vnode){if(vnode.tag==='select'){setSelected(el,binding,vnode.context);// in case the options rendered by v-for have changed,
// it's possible that the value is out-of-sync with the rendered options.
// detect such cases and filter out values that no longer has a matchig
// option in the DOM.
var needReset=el.multiple?binding.value.some(function(v){return hasNoMatchingOption(v,el.options);}):binding.value!==binding.oldValue&&hasNoMatchingOption(binding.value,el.options);if(needReset){trigger(el,'change');}}}};function setSelected(el,binding,vm){var value=binding.value;var isMultiple=el.multiple;if(isMultiple&&!Array.isArray(value)){"development"!=='production'&&warn("<select multiple v-model=\""+binding.expression+"\"> "+"expects an Array value for its binding, but got "+Object.prototype.toString.call(value).slice(8,-1),vm);return;}var selected,option;for(var i=0,l=el.options.length;i<l;i++){option=el.options[i];if(isMultiple){selected=looseIndexOf(value,getValue(option))>-1;if(option.selected!==selected){option.selected=selected;}}else{if(looseEqual(getValue(option),value)){if(el.selectedIndex!==i){el.selectedIndex=i;}return;}}}if(!isMultiple){el.selectedIndex=-1;}}function hasNoMatchingOption(value,options){for(var i=0,l=options.length;i<l;i++){if(looseEqual(getValue(options[i]),value)){return false;}}return true;}function getValue(option){return'_value'in option?option._value:option.value;}function onCompositionStart(e){e.target.composing=true;}function onCompositionEnd(e){e.target.composing=false;trigger(e.target,'input');}function trigger(el,type){var e=document.createEvent('HTMLEvents');e.initEvent(type,true,true);el.dispatchEvent(e);}/*  */// recursively search for possible transition defined inside the component root
function locateNode(vnode){return vnode.child&&(!vnode.data||!vnode.data.transition)?locateNode(vnode.child._vnode):vnode;}var show={bind:function bind(el,ref,vnode){var value=ref.value;vnode=locateNode(vnode);var transition=vnode.data&&vnode.data.transition;if(value&&transition&&!isIE9){enter(vnode);}var originalDisplay=el.style.display==='none'?'':el.style.display;el.style.display=value?originalDisplay:'none';el.__vOriginalDisplay=originalDisplay;},update:function update(el,ref,vnode){var value=ref.value;var oldValue=ref.oldValue;/* istanbul ignore if */if(value===oldValue){return;}vnode=locateNode(vnode);var transition=vnode.data&&vnode.data.transition;if(transition&&!isIE9){if(value){enter(vnode);el.style.display=el.__vOriginalDisplay;}else{leave(vnode,function(){el.style.display='none';});}}else{el.style.display=value?el.__vOriginalDisplay:'none';}}};var platformDirectives={model:model,show:show};/*  */// Provides transition support for a single element/component.
// supports transition mode (out-in / in-out)
var transitionProps={name:String,appear:Boolean,css:Boolean,mode:String,type:String,enterClass:String,leaveClass:String,enterActiveClass:String,leaveActiveClass:String,appearClass:String,appearActiveClass:String};// in case the child is also an abstract component, e.g. <keep-alive>
// we want to recrusively retrieve the real component to be rendered
function getRealChild(vnode){var compOptions=vnode&&vnode.componentOptions;if(compOptions&&compOptions.Ctor.options.abstract){return getRealChild(getFirstComponentChild(compOptions.children));}else{return vnode;}}function extractTransitionData(comp){var data={};var options=comp.$options;// props
for(var key in options.propsData){data[key]=comp[key];}// events.
// extract listeners and pass them directly to the transition methods
var listeners=options._parentListeners;for(var key$1 in listeners){data[camelize(key$1)]=listeners[key$1].fn;}return data;}function placeholder(h,rawChild){return /\d-keep-alive$/.test(rawChild.tag)?h('keep-alive'):null;}function hasParentTransition(vnode){while(vnode=vnode.parent){if(vnode.data.transition){return true;}}}var Transition={name:'transition',props:transitionProps,abstract:true,render:function render(h){var this$1=this;var children=this.$slots.default;if(!children){return;}// filter out text nodes (possible whitespaces)
children=children.filter(function(c){return c.tag;});/* istanbul ignore if */if(!children.length){return;}// warn multiple elements
if("development"!=='production'&&children.length>1){warn('<transition> can only be used on a single element. Use '+'<transition-group> for lists.',this.$parent);}var mode=this.mode;// warn invalid mode
if("development"!=='production'&&mode&&mode!=='in-out'&&mode!=='out-in'){warn('invalid <transition> mode: '+mode,this.$parent);}var rawChild=children[0];// if this is a component root node and the component's
// parent container node also has transition, skip.
if(hasParentTransition(this.$vnode)){return rawChild;}// apply transition data to child
// use getRealChild() to ignore abstract components e.g. keep-alive
var child=getRealChild(rawChild);/* istanbul ignore if */if(!child){return rawChild;}if(this._leaving){return placeholder(h,rawChild);}var key=child.key=child.key==null||child.isStatic?"__v"+(child.tag+this._uid)+"__":child.key;var data=(child.data||(child.data={})).transition=extractTransitionData(this);var oldRawChild=this._vnode;var oldChild=getRealChild(oldRawChild);// mark v-show
// so that the transition module can hand over the control to the directive
if(child.data.directives&&child.data.directives.some(function(d){return d.name==='show';})){child.data.show=true;}if(oldChild&&oldChild.data&&oldChild.key!==key){// replace old child transition data with fresh one
// important for dynamic transitions!
var oldData=oldChild.data.transition=extend({},data);// handle transition mode
if(mode==='out-in'){// return placeholder node and queue update when leave finishes
this._leaving=true;mergeVNodeHook(oldData,'afterLeave',function(){this$1._leaving=false;this$1.$forceUpdate();},key);return placeholder(h,rawChild);}else if(mode==='in-out'){var delayedLeave;var performLeave=function performLeave(){delayedLeave();};mergeVNodeHook(data,'afterEnter',performLeave,key);mergeVNodeHook(data,'enterCancelled',performLeave,key);mergeVNodeHook(oldData,'delayLeave',function(leave){delayedLeave=leave;},key);}}return rawChild;}};/*  */// Provides transition support for list items.
// supports move transitions using the FLIP technique.
// Because the vdom's children update algorithm is "unstable" - i.e.
// it doesn't guarantee the relative positioning of removed elements,
// we force transition-group to update its children into two passes:
// in the first pass, we remove all nodes that need to be removed,
// triggering their leaving transition; in the second pass, we insert/move
// into the final disired state. This way in the second pass removed
// nodes will remain where they should be.
var props=extend({tag:String,moveClass:String},transitionProps);delete props.mode;var TransitionGroup={props:props,render:function render(h){var tag=this.tag||this.$vnode.data.tag||'span';var map=Object.create(null);var prevChildren=this.prevChildren=this.children;var rawChildren=this.$slots.default||[];var children=this.children=[];var transitionData=extractTransitionData(this);for(var i=0;i<rawChildren.length;i++){var c=rawChildren[i];if(c.tag){if(c.key!=null&&String(c.key).indexOf('__vlist')!==0){children.push(c);map[c.key]=c;(c.data||(c.data={})).transition=transitionData;}else{var opts=c.componentOptions;var name=opts?opts.Ctor.options.name||opts.tag:c.tag;warn("<transition-group> children must be keyed: <"+name+">");}}}if(prevChildren){var kept=[];var removed=[];for(var i$1=0;i$1<prevChildren.length;i$1++){var c$1=prevChildren[i$1];c$1.data.transition=transitionData;c$1.data.pos=c$1.elm.getBoundingClientRect();if(map[c$1.key]){kept.push(c$1);}else{removed.push(c$1);}}this.kept=h(tag,null,kept);this.removed=removed;}return h(tag,null,children);},beforeUpdate:function beforeUpdate(){// force removing pass
this.__patch__(this._vnode,this.kept,false,// hydrating
true// removeOnly (!important, avoids unnecessary moves)
);this._vnode=this.kept;},updated:function updated(){var children=this.prevChildren;var moveClass=this.moveClass||this.name+'-move';if(!children.length||!this.hasMove(children[0].elm,moveClass)){return;}// we divide the work into three loops to avoid mixing DOM reads and writes
// in each iteration - which helps prevent layout thrashing.
children.forEach(callPendingCbs);children.forEach(recordPosition);children.forEach(applyTranslation);// force reflow to put everything in position
var f=document.body.offsetHeight;// eslint-disable-line
children.forEach(function(c){if(c.data.moved){var el=c.elm;var s=el.style;addTransitionClass(el,moveClass);s.transform=s.WebkitTransform=s.transitionDuration='';el.addEventListener(transitionEndEvent,el._moveCb=function cb(e){if(!e||/transform$/.test(e.propertyName)){el.removeEventListener(transitionEndEvent,cb);el._moveCb=null;removeTransitionClass(el,moveClass);}});}});},methods:{hasMove:function hasMove(el,moveClass){/* istanbul ignore if */if(!hasTransition){return false;}if(this._hasMove!=null){return this._hasMove;}addTransitionClass(el,moveClass);var info=getTransitionInfo(el);removeTransitionClass(el,moveClass);return this._hasMove=info.hasTransform;}}};function callPendingCbs(c){/* istanbul ignore if */if(c.elm._moveCb){c.elm._moveCb();}/* istanbul ignore if */if(c.elm._enterCb){c.elm._enterCb();}}function recordPosition(c){c.data.newPos=c.elm.getBoundingClientRect();}function applyTranslation(c){var oldPos=c.data.pos;var newPos=c.data.newPos;var dx=oldPos.left-newPos.left;var dy=oldPos.top-newPos.top;if(dx||dy){c.data.moved=true;var s=c.elm.style;s.transform=s.WebkitTransform="translate("+dx+"px,"+dy+"px)";s.transitionDuration='0s';}}var platformComponents={Transition:Transition,TransitionGroup:TransitionGroup};/*  */// install platform specific utils
Vue$3.config.isUnknownElement=isUnknownElement;Vue$3.config.isReservedTag=isReservedTag;Vue$3.config.getTagNamespace=getTagNamespace;Vue$3.config.mustUseProp=mustUseProp;// install platform runtime directives & components
extend(Vue$3.options.directives,platformDirectives);extend(Vue$3.options.components,platformComponents);// install platform patch function
Vue$3.prototype.__patch__=config._isServer?noop:patch$1;// wrap mount
Vue$3.prototype.$mount=function(el,hydrating){el=el&&!config._isServer?query(el):undefined;return this._mount(el,hydrating);};// devtools global hook
/* istanbul ignore next */setTimeout(function(){if(config.devtools){if(devtools){devtools.emit('init',Vue$3);}else if("development"!=='production'&&inBrowser&&/Chrome\/\d+/.test(window.navigator.userAgent)){console.log('Download the Vue Devtools for a better development experience:\n'+'https://github.com/vuejs/vue-devtools');}}},0);/*  */// check whether current browser encodes a char inside attribute values
function shouldDecode(content,encoded){var div=document.createElement('div');div.innerHTML="<div a=\""+content+"\">";return div.innerHTML.indexOf(encoded)>0;}// #3663
// IE encodes newlines inside attribute values while other browsers don't
var shouldDecodeNewlines=inBrowser?shouldDecode('\n','&#10;'):false;/*  */var decoder=document.createElement('div');function decode(html){decoder.innerHTML=html;return decoder.textContent;}/**
 * Not type-checking this file because it's mostly vendor code.
 *//*!
 * HTML Parser By John Resig (ejohn.org)
 * Modified by Juriy "kangax" Zaytsev
 * Original code by Erik Arvidsson, Mozilla Public License
 * http://erik.eae.net/simplehtmlparser/simplehtmlparser.js
 */// Regular Expressions for parsing tags and attributes
var singleAttrIdentifier=/([^\s"'<>\/=]+)/;var singleAttrAssign=/(?:=)/;var singleAttrValues=[// attr value double quotes
/"([^"]*)"+/.source,// attr value, single quotes
/'([^']*)'+/.source,// attr value, no quotes
/([^\s"'=<>`]+)/.source];var attribute=new RegExp('^\\s*'+singleAttrIdentifier.source+'(?:\\s*('+singleAttrAssign.source+')'+'\\s*(?:'+singleAttrValues.join('|')+'))?');// could use https://www.w3.org/TR/1999/REC-xml-names-19990114/#NT-QName
// but for Vue templates we can enforce a simple charset
var ncname='[a-zA-Z_][\\w\\-\\.]*';var qnameCapture='((?:'+ncname+'\\:)?'+ncname+')';var startTagOpen=new RegExp('^<'+qnameCapture);var startTagClose=/^\s*(\/?)>/;var endTag=new RegExp('^<\\/'+qnameCapture+'[^>]*>');var doctype=/^<!DOCTYPE [^>]+>/i;var IS_REGEX_CAPTURING_BROKEN=false;'x'.replace(/x(.)?/g,function(m,g){IS_REGEX_CAPTURING_BROKEN=g==='';});// Special Elements (can contain anything)
var isSpecialTag=makeMap('script,style',true);var reCache={};var ltRE=/&lt;/g;var gtRE=/&gt;/g;var nlRE=/&#10;/g;var ampRE=/&amp;/g;var quoteRE=/&quot;/g;function decodeAttr(value,shouldDecodeNewlines){if(shouldDecodeNewlines){value=value.replace(nlRE,'\n');}return value.replace(ltRE,'<').replace(gtRE,'>').replace(ampRE,'&').replace(quoteRE,'"');}function parseHTML(html,options){var stack=[];var expectHTML=options.expectHTML;var isUnaryTag$$1=options.isUnaryTag||no;var index=0;var last,lastTag;while(html){last=html;// Make sure we're not in a script or style element
if(!lastTag||!isSpecialTag(lastTag)){var textEnd=html.indexOf('<');if(textEnd===0){// Comment:
if(/^<!--/.test(html)){var commentEnd=html.indexOf('-->');if(commentEnd>=0){advance(commentEnd+3);continue;}}// http://en.wikipedia.org/wiki/Conditional_comment#Downlevel-revealed_conditional_comment
if(/^<!\[/.test(html)){var conditionalEnd=html.indexOf(']>');if(conditionalEnd>=0){advance(conditionalEnd+2);continue;}}// Doctype:
var doctypeMatch=html.match(doctype);if(doctypeMatch){advance(doctypeMatch[0].length);continue;}// End tag:
var endTagMatch=html.match(endTag);if(endTagMatch){var curIndex=index;advance(endTagMatch[0].length);parseEndTag(endTagMatch[0],endTagMatch[1],curIndex,index);continue;}// Start tag:
var startTagMatch=parseStartTag();if(startTagMatch){handleStartTag(startTagMatch);continue;}}var text=void 0;if(textEnd>=0){text=html.substring(0,textEnd);advance(textEnd);}else{text=html;html='';}if(options.chars){options.chars(text);}}else{var stackedTag=lastTag.toLowerCase();var reStackedTag=reCache[stackedTag]||(reCache[stackedTag]=new RegExp('([\\s\\S]*?)(</'+stackedTag+'[^>]*>)','i'));var endTagLength=0;var rest=html.replace(reStackedTag,function(all,text,endTag){endTagLength=endTag.length;if(stackedTag!=='script'&&stackedTag!=='style'&&stackedTag!=='noscript'){text=text.replace(/<!--([\s\S]*?)-->/g,'$1').replace(/<!\[CDATA\[([\s\S]*?)\]\]>/g,'$1');}if(options.chars){options.chars(text);}return'';});index+=html.length-rest.length;html=rest;parseEndTag('</'+stackedTag+'>',stackedTag,index-endTagLength,index);}if(html===last){throw new Error('Error parsing template:\n\n'+html);}}// Clean up any remaining tags
parseEndTag();function advance(n){index+=n;html=html.substring(n);}function parseStartTag(){var start=html.match(startTagOpen);if(start){var match={tagName:start[1],attrs:[],start:index};advance(start[0].length);var end,attr;while(!(end=html.match(startTagClose))&&(attr=html.match(attribute))){advance(attr[0].length);match.attrs.push(attr);}if(end){match.unarySlash=end[1];advance(end[0].length);match.end=index;return match;}}}function handleStartTag(match){var tagName=match.tagName;var unarySlash=match.unarySlash;if(expectHTML){if(lastTag==='p'&&isNonPhrasingTag(tagName)){parseEndTag('',lastTag);}if(canBeLeftOpenTag(tagName)&&lastTag===tagName){parseEndTag('',tagName);}}var unary=isUnaryTag$$1(tagName)||tagName==='html'&&lastTag==='head'||!!unarySlash;var l=match.attrs.length;var attrs=new Array(l);for(var i=0;i<l;i++){var args=match.attrs[i];// hackish work around FF bug https://bugzilla.mozilla.org/show_bug.cgi?id=369778
if(IS_REGEX_CAPTURING_BROKEN&&args[0].indexOf('""')===-1){if(args[3]===''){delete args[3];}if(args[4]===''){delete args[4];}if(args[5]===''){delete args[5];}}var value=args[3]||args[4]||args[5]||'';attrs[i]={name:args[1],value:decodeAttr(value,options.shouldDecodeNewlines)};}if(!unary){stack.push({tag:tagName,attrs:attrs});lastTag=tagName;unarySlash='';}if(options.start){options.start(tagName,attrs,unary,match.start,match.end);}}function parseEndTag(tag,tagName,start,end){var pos;if(start==null){start=index;}if(end==null){end=index;}// Find the closest opened tag of the same type
if(tagName){var needle=tagName.toLowerCase();for(pos=stack.length-1;pos>=0;pos--){if(stack[pos].tag.toLowerCase()===needle){break;}}}else{// If no tag name is provided, clean shop
pos=0;}if(pos>=0){// Close all the open elements, up the stack
for(var i=stack.length-1;i>=pos;i--){if(options.end){options.end(stack[i].tag,start,end);}}// Remove the open elements from the stack
stack.length=pos;lastTag=pos&&stack[pos-1].tag;}else if(tagName.toLowerCase()==='br'){if(options.start){options.start(tagName,[],true,start,end);}}else if(tagName.toLowerCase()==='p'){if(options.start){options.start(tagName,[],false,start,end);}if(options.end){options.end(tagName,start,end);}}}}/*  */function parseFilters(exp){var inSingle=false;var inDouble=false;var curly=0;var square=0;var paren=0;var lastFilterIndex=0;var c,prev,i,expression,filters;for(i=0;i<exp.length;i++){prev=c;c=exp.charCodeAt(i);if(inSingle){// check single quote
if(c===0x27&&prev!==0x5C){inSingle=!inSingle;}}else if(inDouble){// check double quote
if(c===0x22&&prev!==0x5C){inDouble=!inDouble;}}else if(c===0x7C&&// pipe
exp.charCodeAt(i+1)!==0x7C&&exp.charCodeAt(i-1)!==0x7C&&!curly&&!square&&!paren){if(expression===undefined){// first filter, end of expression
lastFilterIndex=i+1;expression=exp.slice(0,i).trim();}else{pushFilter();}}else{switch(c){case 0x22:inDouble=true;break;// "
case 0x27:inSingle=true;break;// '
case 0x28:paren++;break;// (
case 0x29:paren--;break;// )
case 0x5B:square++;break;// [
case 0x5D:square--;break;// ]
case 0x7B:curly++;break;// {
case 0x7D:curly--;break;// }
}}}if(expression===undefined){expression=exp.slice(0,i).trim();}else if(lastFilterIndex!==0){pushFilter();}function pushFilter(){(filters||(filters=[])).push(exp.slice(lastFilterIndex,i).trim());lastFilterIndex=i+1;}if(filters){for(i=0;i<filters.length;i++){expression=wrapFilter(expression,filters[i]);}}return expression;}function wrapFilter(exp,filter){var i=filter.indexOf('(');if(i<0){// _f: resolveFilter
return"_f(\""+filter+"\")("+exp+")";}else{var name=filter.slice(0,i);var args=filter.slice(i+1);return"_f(\""+name+"\")("+exp+","+args;}}/*  */var defaultTagRE=/\{\{((?:.|\n)+?)\}\}/g;var regexEscapeRE=/[-.*+?^${}()|[\]\/\\]/g;var buildRegex=cached(function(delimiters){var open=delimiters[0].replace(regexEscapeRE,'\\$&');var close=delimiters[1].replace(regexEscapeRE,'\\$&');return new RegExp(open+'((?:.|\\n)+?)'+close,'g');});function parseText(text,delimiters){var tagRE=delimiters?buildRegex(delimiters):defaultTagRE;if(!tagRE.test(text)){return;}var tokens=[];var lastIndex=tagRE.lastIndex=0;var match,index;while(match=tagRE.exec(text)){index=match.index;// push text token
if(index>lastIndex){tokens.push(JSON.stringify(text.slice(lastIndex,index)));}// tag token
var exp=parseFilters(match[1].trim());tokens.push("_s("+exp+")");lastIndex=index+match[0].length;}if(lastIndex<text.length){tokens.push(JSON.stringify(text.slice(lastIndex)));}return tokens.join('+');}/*  */function baseWarn(msg){console.error("[Vue parser]: "+msg);}function pluckModuleFunction(modules,key){return modules?modules.map(function(m){return m[key];}).filter(function(_){return _;}):[];}function addProp(el,name,value){(el.props||(el.props=[])).push({name:name,value:value});}function addAttr(el,name,value){(el.attrs||(el.attrs=[])).push({name:name,value:value});}function addDirective(el,name,rawName,value,arg,modifiers){(el.directives||(el.directives=[])).push({name:name,rawName:rawName,value:value,arg:arg,modifiers:modifiers});}function addHandler(el,name,value,modifiers,important){// check capture modifier
if(modifiers&&modifiers.capture){delete modifiers.capture;name='!'+name;// mark the event as captured
}var events;if(modifiers&&modifiers.native){delete modifiers.native;events=el.nativeEvents||(el.nativeEvents={});}else{events=el.events||(el.events={});}var newHandler={value:value,modifiers:modifiers};var handlers=events[name];/* istanbul ignore if */if(Array.isArray(handlers)){important?handlers.unshift(newHandler):handlers.push(newHandler);}else if(handlers){events[name]=important?[newHandler,handlers]:[handlers,newHandler];}else{events[name]=newHandler;}}function getBindingAttr(el,name,getStatic){var dynamicValue=getAndRemoveAttr(el,':'+name)||getAndRemoveAttr(el,'v-bind:'+name);if(dynamicValue!=null){return dynamicValue;}else if(getStatic!==false){var staticValue=getAndRemoveAttr(el,name);if(staticValue!=null){return JSON.stringify(staticValue);}}}function getAndRemoveAttr(el,name){var val;if((val=el.attrsMap[name])!=null){var list=el.attrsList;for(var i=0,l=list.length;i<l;i++){if(list[i].name===name){list.splice(i,1);break;}}}return val;}/*  */var dirRE=/^v-|^@|^:/;var forAliasRE=/(.*?)\s+(?:in|of)\s+(.*)/;var forIteratorRE=/\(([^,]*),([^,]*)(?:,([^,]*))?\)/;var bindRE=/^:|^v-bind:/;var onRE=/^@|^v-on:/;var argRE=/:(.*)$/;var modifierRE=/\.[^\.]+/g;var specialNewlineRE=/\u2028|\u2029/g;var decodeHTMLCached=cached(decode);// configurable state
var warn$1;var platformGetTagNamespace;var platformMustUseProp;var platformIsPreTag;var preTransforms;var transforms;var postTransforms;var delimiters;/**
 * Convert HTML string to AST.
 */function parse(template,options){warn$1=options.warn||baseWarn;platformGetTagNamespace=options.getTagNamespace||no;platformMustUseProp=options.mustUseProp||no;platformIsPreTag=options.isPreTag||no;preTransforms=pluckModuleFunction(options.modules,'preTransformNode');transforms=pluckModuleFunction(options.modules,'transformNode');postTransforms=pluckModuleFunction(options.modules,'postTransformNode');delimiters=options.delimiters;var stack=[];var preserveWhitespace=options.preserveWhitespace!==false;var root;var currentParent;var inVPre=false;var inPre=false;var warned=false;parseHTML(template,{expectHTML:options.expectHTML,isUnaryTag:options.isUnaryTag,shouldDecodeNewlines:options.shouldDecodeNewlines,start:function start(tag,attrs,unary){// check namespace.
// inherit parent ns if there is one
var ns=currentParent&&currentParent.ns||platformGetTagNamespace(tag);// handle IE svg bug
/* istanbul ignore if */if(options.isIE&&ns==='svg'){attrs=guardIESVGBug(attrs);}var element={type:1,tag:tag,attrsList:attrs,attrsMap:makeAttrsMap(attrs,options.isIE),parent:currentParent,children:[]};if(ns){element.ns=ns;}if("client"!=='server'&&isForbiddenTag(element)){element.forbidden=true;"development"!=='production'&&warn$1('Templates should only be responsible for mapping the state to the '+'UI. Avoid placing tags with side-effects in your templates, such as '+"<"+tag+">.");}// apply pre-transforms
for(var i=0;i<preTransforms.length;i++){preTransforms[i](element,options);}if(!inVPre){processPre(element);if(element.pre){inVPre=true;}}if(platformIsPreTag(element.tag)){inPre=true;}if(inVPre){processRawAttrs(element);}else{processFor(element);processIf(element);processOnce(element);processKey(element);// determine whether this is a plain element after
// removing structural attributes
element.plain=!element.key&&!attrs.length;processRef(element);processSlot(element);processComponent(element);for(var i$1=0;i$1<transforms.length;i$1++){transforms[i$1](element,options);}processAttrs(element);}function checkRootConstraints(el){{if(el.tag==='slot'||el.tag==='template'){warn$1("Cannot use <"+el.tag+"> as component root element because it may "+'contain multiple nodes:\n'+template);}if(el.attrsMap.hasOwnProperty('v-for')){warn$1('Cannot use v-for on stateful component root element because '+'it renders multiple elements:\n'+template);}}}// tree management
if(!root){root=element;checkRootConstraints(root);}else if("development"!=='production'&&!stack.length&&!warned){// allow 2 root elements with v-if and v-else
if(root.if&&element.else){checkRootConstraints(element);root.elseBlock=element;}else{warned=true;warn$1("Component template should contain exactly one root element:\n\n"+template);}}if(currentParent&&!element.forbidden){if(element.else){processElse(element,currentParent);}else{currentParent.children.push(element);element.parent=currentParent;}}if(!unary){currentParent=element;stack.push(element);}// apply post-transforms
for(var i$2=0;i$2<postTransforms.length;i$2++){postTransforms[i$2](element,options);}},end:function end(){// remove trailing whitespace
var element=stack[stack.length-1];var lastNode=element.children[element.children.length-1];if(lastNode&&lastNode.type===3&&lastNode.text===' '){element.children.pop();}// pop stack
stack.length-=1;currentParent=stack[stack.length-1];// check pre state
if(element.pre){inVPre=false;}if(platformIsPreTag(element.tag)){inPre=false;}},chars:function chars(text){if(!currentParent){if("development"!=='production'&&!warned&&text===template){warned=true;warn$1('Component template requires a root element, rather than just text:\n\n'+template);}return;}text=inPre||text.trim()?decodeHTMLCached(text)// only preserve whitespace if its not right after a starting tag
:preserveWhitespace&&currentParent.children.length?' ':'';if(text){var expression;if(!inVPre&&text!==' '&&(expression=parseText(text,delimiters))){currentParent.children.push({type:2,expression:expression,text:text});}else{// #3895 special character
text=text.replace(specialNewlineRE,'');currentParent.children.push({type:3,text:text});}}}});return root;}function processPre(el){if(getAndRemoveAttr(el,'v-pre')!=null){el.pre=true;}}function processRawAttrs(el){var l=el.attrsList.length;if(l){var attrs=el.attrs=new Array(l);for(var i=0;i<l;i++){attrs[i]={name:el.attrsList[i].name,value:JSON.stringify(el.attrsList[i].value)};}}else if(!el.pre){// non root node in pre blocks with no attributes
el.plain=true;}}function processKey(el){var exp=getBindingAttr(el,'key');if(exp){if("development"!=='production'&&el.tag==='template'){warn$1("<template> cannot be keyed. Place the key on real elements instead.");}el.key=exp;}}function processRef(el){var ref=getBindingAttr(el,'ref');if(ref){el.ref=ref;el.refInFor=checkInFor(el);}}function processFor(el){var exp;if(exp=getAndRemoveAttr(el,'v-for')){var inMatch=exp.match(forAliasRE);if(!inMatch){"development"!=='production'&&warn$1("Invalid v-for expression: "+exp);return;}el.for=inMatch[2].trim();var alias=inMatch[1].trim();var iteratorMatch=alias.match(forIteratorRE);if(iteratorMatch){el.alias=iteratorMatch[1].trim();el.iterator1=iteratorMatch[2].trim();if(iteratorMatch[3]){el.iterator2=iteratorMatch[3].trim();}}else{el.alias=alias;}}}function processIf(el){var exp=getAndRemoveAttr(el,'v-if');if(exp){el.if=exp;}if(getAndRemoveAttr(el,'v-else')!=null){el.else=true;}}function processElse(el,parent){var prev=findPrevElement(parent.children);if(prev&&prev.if){prev.elseBlock=el;}else{warn$1("v-else used on element <"+el.tag+"> without corresponding v-if.");}}function processOnce(el){var once=getAndRemoveAttr(el,'v-once');if(once!=null){el.once=true;}}function processSlot(el){if(el.tag==='slot'){el.slotName=getBindingAttr(el,'name');}else{var slotTarget=getBindingAttr(el,'slot');if(slotTarget){el.slotTarget=slotTarget;}}}function processComponent(el){var binding;if(binding=getBindingAttr(el,'is')){el.component=binding;}if(getAndRemoveAttr(el,'inline-template')!=null){el.inlineTemplate=true;}}function processAttrs(el){var list=el.attrsList;var i,l,name,rawName,value,arg,modifiers,isProp;for(i=0,l=list.length;i<l;i++){name=rawName=list[i].name;value=list[i].value;if(dirRE.test(name)){// mark element as dynamic
el.hasBindings=true;// modifiers
modifiers=parseModifiers(name);if(modifiers){name=name.replace(modifierRE,'');}if(bindRE.test(name)){// v-bind
name=name.replace(bindRE,'');if(modifiers&&modifiers.prop){isProp=true;name=camelize(name);if(name==='innerHtml'){name='innerHTML';}}if(isProp||platformMustUseProp(name)){addProp(el,name,value);}else{addAttr(el,name,value);}}else if(onRE.test(name)){// v-on
name=name.replace(onRE,'');addHandler(el,name,value,modifiers);}else{// normal directives
name=name.replace(dirRE,'');// parse arg
var argMatch=name.match(argRE);if(argMatch&&(arg=argMatch[1])){name=name.slice(0,-(arg.length+1));}addDirective(el,name,rawName,value,arg,modifiers);if("development"!=='production'&&name==='model'){checkForAliasModel(el,value);}}}else{// literal attribute
{var expression=parseText(value,delimiters);if(expression){warn$1(name+"=\""+value+"\": "+'Interpolation inside attributes has been deprecated. '+'Use v-bind or the colon shorthand instead.');}}addAttr(el,name,JSON.stringify(value));}}}function checkInFor(el){var parent=el;while(parent){if(parent.for!==undefined){return true;}parent=parent.parent;}return false;}function parseModifiers(name){var match=name.match(modifierRE);if(match){var ret={};match.forEach(function(m){ret[m.slice(1)]=true;});return ret;}}function makeAttrsMap(attrs,isIE){var map={};for(var i=0,l=attrs.length;i<l;i++){if("development"!=='production'&&map[attrs[i].name]&&!isIE){warn$1('duplicate attribute: '+attrs[i].name);}map[attrs[i].name]=attrs[i].value;}return map;}function findPrevElement(children){var i=children.length;while(i--){if(children[i].tag){return children[i];}}}function isForbiddenTag(el){return el.tag==='style'||el.tag==='script'&&(!el.attrsMap.type||el.attrsMap.type==='text/javascript');}var ieNSBug=/^xmlns:NS\d+/;var ieNSPrefix=/^NS\d+:/;/* istanbul ignore next */function guardIESVGBug(attrs){var res=[];for(var i=0;i<attrs.length;i++){var attr=attrs[i];if(!ieNSBug.test(attr.name)){attr.name=attr.name.replace(ieNSPrefix,'');res.push(attr);}}return res;}function checkForAliasModel(el,value){var _el=el;while(_el){if(_el.for&&_el.alias===value){warn$1("<"+el.tag+" v-model=\""+value+"\">: "+"You are binding v-model directly to a v-for iteration alias. "+"This will not be able to modify the v-for source array because "+"writing to the alias is like modifying a function local variable. "+"Consider using an array of objects and use v-model on an object property instead.");}_el=_el.parent;}}/*  */var isStaticKey;var isPlatformReservedTag;var genStaticKeysCached=cached(genStaticKeys$1);/**
 * Goal of the optimizier: walk the generated template AST tree
 * and detect sub-trees that are purely static, i.e. parts of
 * the DOM that never needs to change.
 *
 * Once we detect these sub-trees, we can:
 *
 * 1. Hoist them into constants, so that we no longer need to
 *    create fresh nodes for them on each re-render;
 * 2. Completely skip them in the patching process.
 */function optimize(root,options){if(!root){return;}isStaticKey=genStaticKeysCached(options.staticKeys||'');isPlatformReservedTag=options.isReservedTag||function(){return false;};// first pass: mark all non-static nodes.
markStatic(root);// second pass: mark static roots.
markStaticRoots(root,false);}function genStaticKeys$1(keys){return makeMap('type,tag,attrsList,attrsMap,plain,parent,children,attrs'+(keys?','+keys:''));}function markStatic(node){node.static=isStatic(node);if(node.type===1){for(var i=0,l=node.children.length;i<l;i++){var child=node.children[i];markStatic(child);if(!child.static){node.static=false;}}}}function markStaticRoots(node,isInFor){if(node.type===1){if(node.once||node.static){node.staticRoot=true;node.staticInFor=isInFor;return;}if(node.children){for(var i=0,l=node.children.length;i<l;i++){markStaticRoots(node.children[i],isInFor||!!node.for);}}}}function isStatic(node){if(node.type===2){// expression
return false;}if(node.type===3){// text
return true;}return!!(node.pre||!node.hasBindings&&// no dynamic bindings
!node.if&&!node.for&&// not v-if or v-for or v-else
!isBuiltInTag(node.tag)&&// not a built-in
isPlatformReservedTag(node.tag)&&// not a component
!isDirectChildOfTemplateFor(node)&&Object.keys(node).every(isStaticKey));}function isDirectChildOfTemplateFor(node){while(node.parent){node=node.parent;if(node.tag!=='template'){return false;}if(node.for){return true;}}return false;}/*  */var simplePathRE=/^\s*[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['.*?'\]|\[".*?"\]|\[\d+\]|\[[A-Za-z_$][\w$]*\])*\s*$/;// keyCode aliases
var keyCodes={esc:27,tab:9,enter:13,space:32,up:38,left:37,right:39,down:40,'delete':[8,46]};var modifierCode={stop:'$event.stopPropagation();',prevent:'$event.preventDefault();',self:'if($event.target !== $event.currentTarget)return;'};function genHandlers(events,native){var res=native?'nativeOn:{':'on:{';for(var name in events){res+="\""+name+"\":"+genHandler(events[name])+",";}return res.slice(0,-1)+'}';}function genHandler(handler){if(!handler){return'function(){}';}else if(Array.isArray(handler)){return"["+handler.map(genHandler).join(',')+"]";}else if(!handler.modifiers){return simplePathRE.test(handler.value)?handler.value:"function($event){"+handler.value+"}";}else{var code='';var keys=[];for(var key in handler.modifiers){if(modifierCode[key]){code+=modifierCode[key];}else{keys.push(key);}}if(keys.length){code=genKeyFilter(keys)+code;}var handlerCode=simplePathRE.test(handler.value)?handler.value+'($event)':handler.value;return'function($event){'+code+handlerCode+'}';}}function genKeyFilter(keys){var code=keys.length===1?normalizeKeyCode(keys[0]):Array.prototype.concat.apply([],keys.map(normalizeKeyCode));if(Array.isArray(code)){return"if("+code.map(function(c){return"$event.keyCode!=="+c;}).join('&&')+")return;";}else{return"if($event.keyCode!=="+code+")return;";}}function normalizeKeyCode(key){return parseInt(key,10)||// number keyCode
keyCodes[key]||// built-in alias
"_k("+JSON.stringify(key)+")"// custom alias
;}/*  */function bind$2(el,dir){el.wrapData=function(code){return"_b("+code+","+dir.value+(dir.modifiers&&dir.modifiers.prop?',true':'')+")";};}var baseDirectives={bind:bind$2,cloak:noop};/*  */// configurable state
var warn$2;var transforms$1;var dataGenFns;var platformDirectives$1;var staticRenderFns;var currentOptions;function generate(ast,options){// save previous staticRenderFns so generate calls can be nested
var prevStaticRenderFns=staticRenderFns;var currentStaticRenderFns=staticRenderFns=[];currentOptions=options;warn$2=options.warn||baseWarn;transforms$1=pluckModuleFunction(options.modules,'transformCode');dataGenFns=pluckModuleFunction(options.modules,'genData');platformDirectives$1=options.directives||{};var code=ast?genElement(ast):'_h("div")';staticRenderFns=prevStaticRenderFns;return{render:"with(this){return "+code+"}",staticRenderFns:currentStaticRenderFns};}function genElement(el){if(el.staticRoot&&!el.staticProcessed){// hoist static sub-trees out
el.staticProcessed=true;staticRenderFns.push("with(this){return "+genElement(el)+"}");return"_m("+(staticRenderFns.length-1)+(el.staticInFor?',true':'')+")";}else if(el.for&&!el.forProcessed){return genFor(el);}else if(el.if&&!el.ifProcessed){return genIf(el);}else if(el.tag==='template'&&!el.slotTarget){return genChildren(el)||'void 0';}else if(el.tag==='slot'){return genSlot(el);}else{// component or element
var code;if(el.component){code=genComponent(el);}else{var data=genData(el);var children=el.inlineTemplate?null:genChildren(el);code="_h('"+el.tag+"'"+(data?","+data:'')+(children?","+children:'')+")";}// module transforms
for(var i=0;i<transforms$1.length;i++){code=transforms$1[i](el,code);}return code;}}function genIf(el){var exp=el.if;el.ifProcessed=true;// avoid recursion
return"("+exp+")?"+genElement(el)+":"+genElse(el);}function genElse(el){return el.elseBlock?genElement(el.elseBlock):'_e()';}function genFor(el){var exp=el.for;var alias=el.alias;var iterator1=el.iterator1?","+el.iterator1:'';var iterator2=el.iterator2?","+el.iterator2:'';el.forProcessed=true;// avoid recursion
return"_l(("+exp+"),"+"function("+alias+iterator1+iterator2+"){"+"return "+genElement(el)+'})';}function genData(el){if(el.plain){return;}var data='{';// directives first.
// directives may mutate the el's other properties before they are generated.
var dirs=genDirectives(el);if(dirs){data+=dirs+',';}// key
if(el.key){data+="key:"+el.key+",";}// ref
if(el.ref){data+="ref:"+el.ref+",";}if(el.refInFor){data+="refInFor:true,";}// record original tag name for components using "is" attribute
if(el.component){data+="tag:\""+el.tag+"\",";}// slot target
if(el.slotTarget){data+="slot:"+el.slotTarget+",";}// module data generation functions
for(var i=0;i<dataGenFns.length;i++){data+=dataGenFns[i](el);}// attributes
if(el.attrs){data+="attrs:{"+genProps(el.attrs)+"},";}// DOM props
if(el.props){data+="domProps:{"+genProps(el.props)+"},";}// event handlers
if(el.events){data+=genHandlers(el.events)+",";}if(el.nativeEvents){data+=genHandlers(el.nativeEvents,true)+",";}// inline-template
if(el.inlineTemplate){var ast=el.children[0];if("development"!=='production'&&(el.children.length>1||ast.type!==1)){warn$2('Inline-template components must have exactly one child element.');}if(ast.type===1){var inlineRenderFns=generate(ast,currentOptions);data+="inlineTemplate:{render:function(){"+inlineRenderFns.render+"},staticRenderFns:["+inlineRenderFns.staticRenderFns.map(function(code){return"function(){"+code+"}";}).join(',')+"]}";}}data=data.replace(/,$/,'')+'}';// v-bind data wrap
if(el.wrapData){data=el.wrapData(data);}return data;}function genDirectives(el){var dirs=el.directives;if(!dirs){return;}var res='directives:[';var hasRuntime=false;var i,l,dir,needRuntime;for(i=0,l=dirs.length;i<l;i++){dir=dirs[i];needRuntime=true;var gen=platformDirectives$1[dir.name]||baseDirectives[dir.name];if(gen){// compile-time directive that manipulates AST.
// returns true if it also needs a runtime counterpart.
needRuntime=!!gen(el,dir,warn$2);}if(needRuntime){hasRuntime=true;res+="{name:\""+dir.name+"\",rawName:\""+dir.rawName+"\""+(dir.value?",value:("+dir.value+"),expression:"+JSON.stringify(dir.value):'')+(dir.arg?",arg:\""+dir.arg+"\"":'')+(dir.modifiers?",modifiers:"+JSON.stringify(dir.modifiers):'')+"},";}}if(hasRuntime){return res.slice(0,-1)+']';}}function genChildren(el){if(el.children.length){return'['+el.children.map(genNode).join(',')+']';}}function genNode(node){if(node.type===1){return genElement(node);}else{return genText(node);}}function genText(text){return text.type===2?text.expression// no need for () because already wrapped in _s()
:JSON.stringify(text.text);}function genSlot(el){var slotName=el.slotName||'"default"';var children=genChildren(el);return children?"_t("+slotName+","+children+")":"_t("+slotName+")";}function genComponent(el){var children=el.inlineTemplate?null:genChildren(el);return"_h("+el.component+","+genData(el)+(children?","+children:'')+")";}function genProps(props){var res='';for(var i=0;i<props.length;i++){var prop=props[i];res+="\""+prop.name+"\":"+prop.value+",";}return res.slice(0,-1);}/*  *//**
 * Compile a template.
 */function compile$1(template,options){var ast=parse(template.trim(),options);optimize(ast,options);var code=generate(ast,options);return{ast:ast,render:code.render,staticRenderFns:code.staticRenderFns};}/*  */// operators like typeof, instanceof and in are allowed
var prohibitedKeywordRE=new RegExp('\\b'+('do,if,for,let,new,try,var,case,else,with,await,break,catch,class,const,'+'super,throw,while,yield,delete,export,import,return,switch,default,'+'extends,finally,continue,debugger,function,arguments').split(',').join('\\b|\\b')+'\\b');// check valid identifier for v-for
var identRE=/[A-Za-z_$][\w$]*/;// strip strings in expressions
var stripStringRE=/'(?:[^'\\]|\\.)*'|"(?:[^"\\]|\\.)*"|`(?:[^`\\]|\\.)*\$\{|\}(?:[^`\\]|\\.)*`|`(?:[^`\\]|\\.)*`/g;// detect problematic expressions in a template
function detectErrors(ast){var errors=[];if(ast){checkNode(ast,errors);}return errors;}function checkNode(node,errors){if(node.type===1){for(var name in node.attrsMap){if(dirRE.test(name)){var value=node.attrsMap[name];if(value){if(name==='v-for'){checkFor(node,"v-for=\""+value+"\"",errors);}else{checkExpression(value,name+"=\""+value+"\"",errors);}}}}if(node.children){for(var i=0;i<node.children.length;i++){checkNode(node.children[i],errors);}}}else if(node.type===2){checkExpression(node.expression,node.text,errors);}}function checkFor(node,text,errors){checkExpression(node.for||'',text,errors);checkIdentifier(node.alias,'v-for alias',text,errors);checkIdentifier(node.iterator1,'v-for iterator',text,errors);checkIdentifier(node.iterator2,'v-for iterator',text,errors);}function checkIdentifier(ident,type,text,errors){if(typeof ident==='string'&&!identRE.test(ident)){errors.push("- invalid "+type+" \""+ident+"\" in expression: "+text);}}function checkExpression(exp,text,errors){try{new Function("return "+exp);}catch(e){var keywordMatch=exp.replace(stripStringRE,'').match(prohibitedKeywordRE);if(keywordMatch){errors.push("- avoid using JavaScript keyword as property name: "+"\""+keywordMatch[0]+"\" in expression "+text);}else{errors.push("- invalid expression: "+text);}}}/*  */function transformNode(el,options){var warn=options.warn||baseWarn;var staticClass=getAndRemoveAttr(el,'class');if("development"!=='production'&&staticClass){var expression=parseText(staticClass,options.delimiters);if(expression){warn("class=\""+staticClass+"\": "+'Interpolation inside attributes has been deprecated. '+'Use v-bind or the colon shorthand instead.');}}if(staticClass){el.staticClass=JSON.stringify(staticClass);}var classBinding=getBindingAttr(el,'class',false/* getStatic */);if(classBinding){el.classBinding=classBinding;}}function genData$1(el){var data='';if(el.staticClass){data+="staticClass:"+el.staticClass+",";}if(el.classBinding){data+="class:"+el.classBinding+",";}return data;}var klass$1={staticKeys:['staticClass'],transformNode:transformNode,genData:genData$1};/*  */function transformNode$1(el){var styleBinding=getBindingAttr(el,'style',false/* getStatic */);if(styleBinding){el.styleBinding=styleBinding;}}function genData$2(el){return el.styleBinding?"style:("+el.styleBinding+"),":'';}var style$1={transformNode:transformNode$1,genData:genData$2};var modules$1=[klass$1,style$1];/*  */var warn$3;function model$1(el,dir,_warn){warn$3=_warn;var value=dir.value;var modifiers=dir.modifiers;var tag=el.tag;var type=el.attrsMap.type;{var dynamicType=el.attrsMap['v-bind:type']||el.attrsMap[':type'];if(tag==='input'&&dynamicType){warn$3("<input :type=\""+dynamicType+"\" v-model=\""+value+"\">:\n"+"v-model does not support dynamic input types. Use v-if branches instead.");}}if(tag==='select'){genSelect(el,value);}else if(tag==='input'&&type==='checkbox'){genCheckboxModel(el,value);}else if(tag==='input'&&type==='radio'){genRadioModel(el,value);}else{genDefaultModel(el,value,modifiers);}// ensure runtime directive metadata
return true;}function genCheckboxModel(el,value){if("development"!=='production'&&el.attrsMap.checked!=null){warn$3("<"+el.tag+" v-model=\""+value+"\" checked>:\n"+"inline checked attributes will be ignored when using v-model. "+'Declare initial values in the component\'s data option instead.');}var valueBinding=getBindingAttr(el,'value')||'null';var trueValueBinding=getBindingAttr(el,'true-value')||'true';var falseValueBinding=getBindingAttr(el,'false-value')||'false';addProp(el,'checked',"Array.isArray("+value+")"+"?_i("+value+","+valueBinding+")>-1"+":_q("+value+","+trueValueBinding+")");addHandler(el,'change',"var $$a="+value+","+'$$el=$event.target,'+"$$c=$$el.checked?("+trueValueBinding+"):("+falseValueBinding+");"+'if(Array.isArray($$a)){'+"var $$v="+valueBinding+","+'$$i=_i($$a,$$v);'+"if($$c){$$i<0&&("+value+"=$$a.concat($$v))}"+"else{$$i>-1&&("+value+"=$$a.slice(0,$$i).concat($$a.slice($$i+1)))}"+"}else{"+value+"=$$c}",null,true);}function genRadioModel(el,value){if("development"!=='production'&&el.attrsMap.checked!=null){warn$3("<"+el.tag+" v-model=\""+value+"\" checked>:\n"+"inline checked attributes will be ignored when using v-model. "+'Declare initial values in the component\'s data option instead.');}var valueBinding=getBindingAttr(el,'value')||'null';addProp(el,'checked',"_q("+value+","+valueBinding+")");addHandler(el,'change',value+"="+valueBinding,null,true);}function genDefaultModel(el,value,modifiers){{if(el.tag==='input'&&el.attrsMap.value){warn$3("<"+el.tag+" v-model=\""+value+"\" value=\""+el.attrsMap.value+"\">:\n"+'inline value attributes will be ignored when using v-model. '+'Declare initial values in the component\'s data option instead.');}if(el.tag==='textarea'&&el.children.length){warn$3("<textarea v-model=\""+value+"\">:\n"+'inline content inside <textarea> will be ignored when using v-model. '+'Declare initial values in the component\'s data option instead.');}}var type=el.attrsMap.type;var ref=modifiers||{};var lazy=ref.lazy;var number=ref.number;var trim=ref.trim;var event=lazy||isIE&&type==='range'?'change':'input';var needCompositionGuard=!lazy&&type!=='range';var isNative=el.tag==='input'||el.tag==='textarea';var valueExpression=isNative?"$event.target.value"+(trim?'.trim()':''):"$event";var code=number||type==='number'?value+"=_n("+valueExpression+")":value+"="+valueExpression;if(isNative&&needCompositionGuard){code="if($event.target.composing)return;"+code;}// inputs with type="file" are read only and setting the input's
// value will throw an error.
if("development"!=='production'&&type==='file'){warn$3("<"+el.tag+" v-model=\""+value+"\" type=\"file\">:\n"+"File inputs are read only. Use a v-on:change listener instead.");}addProp(el,'value',isNative?"_s("+value+")":"("+value+")");addHandler(el,event,code,null,true);}function genSelect(el,value){{el.children.some(checkOptionWarning);}var code=value+"=Array.prototype.filter"+".call($event.target.options,function(o){return o.selected})"+".map(function(o){return \"_value\" in o ? o._value : o.value})"+(el.attrsMap.multiple==null?'[0]':'');addHandler(el,'change',code,null,true);}function checkOptionWarning(option){if(option.type===1&&option.tag==='option'&&option.attrsMap.selected!=null){warn$3("<select v-model=\""+option.parent.attrsMap['v-model']+"\">:\n"+'inline selected attributes on <option> will be ignored when using v-model. '+'Declare initial values in the component\'s data option instead.');return true;}return false;}/*  */function text(el,dir){if(dir.value){addProp(el,'textContent',"_s("+dir.value+")");}}/*  */function html(el,dir){if(dir.value){addProp(el,'innerHTML',"_s("+dir.value+")");}}var directives$1={model:model$1,text:text,html:html};/*  */var cache=Object.create(null);var baseOptions={isIE:isIE,expectHTML:true,modules:modules$1,staticKeys:genStaticKeys(modules$1),directives:directives$1,isReservedTag:isReservedTag,isUnaryTag:isUnaryTag,mustUseProp:mustUseProp,getTagNamespace:getTagNamespace,isPreTag:isPreTag};function compile$$1(template,options){options=options?extend(extend({},baseOptions),options):baseOptions;return compile$1(template,options);}function compileToFunctions(template,options,vm){var _warn=options&&options.warn||warn;// detect possible CSP restriction
/* istanbul ignore if */{try{new Function('return 1');}catch(e){if(e.toString().match(/unsafe-eval|CSP/)){_warn('It seems you are using the standalone build of Vue.js in an '+'environment with Content Security Policy that prohibits unsafe-eval. '+'The template compiler cannot work in this environment. Consider '+'relaxing the policy to allow unsafe-eval or pre-compiling your '+'templates into render functions.');}}}var key=options&&options.delimiters?String(options.delimiters)+template:template;if(cache[key]){return cache[key];}var res={};var compiled=compile$$1(template,options);res.render=makeFunction(compiled.render);var l=compiled.staticRenderFns.length;res.staticRenderFns=new Array(l);for(var i=0;i<l;i++){res.staticRenderFns[i]=makeFunction(compiled.staticRenderFns[i]);}{if(res.render===noop||res.staticRenderFns.some(function(fn){return fn===noop;})){_warn("failed to compile template:\n\n"+template+"\n\n"+detectErrors(compiled.ast).join('\n')+'\n\n',vm);}}return cache[key]=res;}function makeFunction(code){try{return new Function(code);}catch(e){return noop;}}/*  */var idToTemplate=cached(function(id){var el=query(id);return el&&el.innerHTML;});var mount=Vue$3.prototype.$mount;Vue$3.prototype.$mount=function(el,hydrating){el=el&&query(el);/* istanbul ignore if */if(el===document.body||el===document.documentElement){"development"!=='production'&&warn("Do not mount Vue to <html> or <body> - mount to normal elements instead.");return this;}var options=this.$options;// resolve template/el and convert to render function
if(!options.render){var template=options.template;if(template){if(typeof template==='string'){if(template.charAt(0)==='#'){template=idToTemplate(template);}}else if(template.nodeType){template=template.innerHTML;}else{{warn('invalid template option:'+template,this);}return this;}}else if(el){template=getOuterHTML(el);}if(template){var ref=compileToFunctions(template,{warn:warn,shouldDecodeNewlines:shouldDecodeNewlines,delimiters:options.delimiters},this);var render=ref.render;var staticRenderFns=ref.staticRenderFns;options.render=render;options.staticRenderFns=staticRenderFns;}}return mount.call(this,el,hydrating);};/**
 * Get outerHTML of elements, taking care
 * of SVG elements in IE as well.
 */function getOuterHTML(el){if(el.outerHTML){return el.outerHTML;}else{var container=document.createElement('div');container.appendChild(el.cloneNode(true));return container.innerHTML;}}Vue$3.compile=compileToFunctions;return Vue$3;});

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

var __vue_exports__, __vue_options__

/* styles */
__webpack_require__(127)

/* script */
__vue_exports__ = __webpack_require__(115)

/* template */
var __vue_template__ = __webpack_require__(123)
__vue_options__ = __vue_exports__ = __vue_exports__ || {}
if (
  typeof __vue_exports__.default === "object" ||
  typeof __vue_exports__.default === "function"
) {
if (Object.keys(__vue_exports__).some(function (key) { return key !== "default" && key !== "__esModule" })) {console.error("named exports are not supported in *.vue files.")}
__vue_options__ = __vue_exports__ = __vue_exports__.default
}
if (typeof __vue_options__ === "function") {
  __vue_options__ = __vue_options__.options
}
__vue_options__.__file = "/Users/jan/Sites/FlexyAdmin/FlexyAdmin/sys/flexyadmin/assets/js/vue-components/vue-pagination.vue"
__vue_options__.render = __vue_template__.render
__vue_options__.staticRenderFns = __vue_template__.staticRenderFns

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7d03110e", __vue_options__)
  } else {
    hotAPI.reload("data-v-7d03110e", __vue_options__)
  }
})()}
if (__vue_options__.functional) {console.error("[vue-loader] vue-pagination.vue: functional components are not supported and should be defined in plain js files using render functions.")}

module.exports = __vue_exports__


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(Buffer, global) {/*!
 * The buffer module from node.js, for the browser.
 *
 * @author   Feross Aboukhadijeh <feross@feross.org> <http://feross.org>
 * @license  MIT
 *//* eslint-disable no-proto */'use strict';var base64=__webpack_require__(24);var ieee754=__webpack_require__(25);var isArray=__webpack_require__(26);exports.Buffer=Buffer;exports.SlowBuffer=SlowBuffer;exports.INSPECT_MAX_BYTES=50;/**
 * If `Buffer.TYPED_ARRAY_SUPPORT`:
 *   === true    Use Uint8Array implementation (fastest)
 *   === false   Use Object implementation (most compatible, even IE6)
 *
 * Browsers that support typed arrays are IE 10+, Firefox 4+, Chrome 7+, Safari 5.1+,
 * Opera 11.6+, iOS 4.2+.
 *
 * Due to various browser bugs, sometimes the Object implementation will be used even
 * when the browser supports typed arrays.
 *
 * Note:
 *
 *   - Firefox 4-29 lacks support for adding new properties to `Uint8Array` instances,
 *     See: https://bugzilla.mozilla.org/show_bug.cgi?id=695438.
 *
 *   - Chrome 9-10 is missing the `TypedArray.prototype.subarray` function.
 *
 *   - IE10 has a broken `TypedArray.prototype.subarray` function which returns arrays of
 *     incorrect length in some situations.

 * We detect these buggy browsers and set `Buffer.TYPED_ARRAY_SUPPORT` to `false` so they
 * get the Object implementation, which is slower but behaves correctly.
 */Buffer.TYPED_ARRAY_SUPPORT=global.TYPED_ARRAY_SUPPORT!==undefined?global.TYPED_ARRAY_SUPPORT:typedArraySupport();/*
 * Export kMaxLength after typed array support is determined.
 */exports.kMaxLength=kMaxLength();function typedArraySupport(){try{var arr=new Uint8Array(1);arr.__proto__={__proto__:Uint8Array.prototype,foo:function foo(){return 42;}};return arr.foo()===42&&// typed array instances can be augmented
typeof arr.subarray==='function'&&// chrome 9-10 lack `subarray`
arr.subarray(1,1).byteLength===0;// ie10 has broken `subarray`
}catch(e){return false;}}function kMaxLength(){return Buffer.TYPED_ARRAY_SUPPORT?0x7fffffff:0x3fffffff;}function createBuffer(that,length){if(kMaxLength()<length){throw new RangeError('Invalid typed array length');}if(Buffer.TYPED_ARRAY_SUPPORT){// Return an augmented `Uint8Array` instance, for best performance
that=new Uint8Array(length);that.__proto__=Buffer.prototype;}else{// Fallback: Return an object instance of the Buffer class
if(that===null){that=new Buffer(length);}that.length=length;}return that;}/**
 * The Buffer constructor returns instances of `Uint8Array` that have their
 * prototype changed to `Buffer.prototype`. Furthermore, `Buffer` is a subclass of
 * `Uint8Array`, so the returned instances will have all the node `Buffer` methods
 * and the `Uint8Array` methods. Square bracket notation works as expected -- it
 * returns a single octet.
 *
 * The `Uint8Array` prototype remains unmodified.
 */function Buffer(arg,encodingOrOffset,length){if(!Buffer.TYPED_ARRAY_SUPPORT&&!(this instanceof Buffer)){return new Buffer(arg,encodingOrOffset,length);}// Common case.
if(typeof arg==='number'){if(typeof encodingOrOffset==='string'){throw new Error('If encoding is specified then the first argument must be a string');}return allocUnsafe(this,arg);}return from(this,arg,encodingOrOffset,length);}Buffer.poolSize=8192;// not used by this implementation
// TODO: Legacy, not needed anymore. Remove in next major version.
Buffer._augment=function(arr){arr.__proto__=Buffer.prototype;return arr;};function from(that,value,encodingOrOffset,length){if(typeof value==='number'){throw new TypeError('"value" argument must not be a number');}if(typeof ArrayBuffer!=='undefined'&&value instanceof ArrayBuffer){return fromArrayBuffer(that,value,encodingOrOffset,length);}if(typeof value==='string'){return fromString(that,value,encodingOrOffset);}return fromObject(that,value);}/**
 * Functionally equivalent to Buffer(arg, encoding) but throws a TypeError
 * if value is a number.
 * Buffer.from(str[, encoding])
 * Buffer.from(array)
 * Buffer.from(buffer)
 * Buffer.from(arrayBuffer[, byteOffset[, length]])
 **/Buffer.from=function(value,encodingOrOffset,length){return from(null,value,encodingOrOffset,length);};if(Buffer.TYPED_ARRAY_SUPPORT){Buffer.prototype.__proto__=Uint8Array.prototype;Buffer.__proto__=Uint8Array;if(typeof Symbol!=='undefined'&&Symbol.species&&Buffer[Symbol.species]===Buffer){// Fix subarray() in ES2016. See: https://github.com/feross/buffer/pull/97
Object.defineProperty(Buffer,Symbol.species,{value:null,configurable:true});}}function assertSize(size){if(typeof size!=='number'){throw new TypeError('"size" argument must be a number');}else if(size<0){throw new RangeError('"size" argument must not be negative');}}function alloc(that,size,fill,encoding){assertSize(size);if(size<=0){return createBuffer(that,size);}if(fill!==undefined){// Only pay attention to encoding if it's a string. This
// prevents accidentally sending in a number that would
// be interpretted as a start offset.
return typeof encoding==='string'?createBuffer(that,size).fill(fill,encoding):createBuffer(that,size).fill(fill);}return createBuffer(that,size);}/**
 * Creates a new filled Buffer instance.
 * alloc(size[, fill[, encoding]])
 **/Buffer.alloc=function(size,fill,encoding){return alloc(null,size,fill,encoding);};function allocUnsafe(that,size){assertSize(size);that=createBuffer(that,size<0?0:checked(size)|0);if(!Buffer.TYPED_ARRAY_SUPPORT){for(var i=0;i<size;++i){that[i]=0;}}return that;}/**
 * Equivalent to Buffer(num), by default creates a non-zero-filled Buffer instance.
 * */Buffer.allocUnsafe=function(size){return allocUnsafe(null,size);};/**
 * Equivalent to SlowBuffer(num), by default creates a non-zero-filled Buffer instance.
 */Buffer.allocUnsafeSlow=function(size){return allocUnsafe(null,size);};function fromString(that,string,encoding){if(typeof encoding!=='string'||encoding===''){encoding='utf8';}if(!Buffer.isEncoding(encoding)){throw new TypeError('"encoding" must be a valid string encoding');}var length=byteLength(string,encoding)|0;that=createBuffer(that,length);var actual=that.write(string,encoding);if(actual!==length){// Writing a hex string, for example, that contains invalid characters will
// cause everything after the first invalid character to be ignored. (e.g.
// 'abxxcd' will be treated as 'ab')
that=that.slice(0,actual);}return that;}function fromArrayLike(that,array){var length=array.length<0?0:checked(array.length)|0;that=createBuffer(that,length);for(var i=0;i<length;i+=1){that[i]=array[i]&255;}return that;}function fromArrayBuffer(that,array,byteOffset,length){array.byteLength;// this throws if `array` is not a valid ArrayBuffer
if(byteOffset<0||array.byteLength<byteOffset){throw new RangeError('\'offset\' is out of bounds');}if(array.byteLength<byteOffset+(length||0)){throw new RangeError('\'length\' is out of bounds');}if(byteOffset===undefined&&length===undefined){array=new Uint8Array(array);}else if(length===undefined){array=new Uint8Array(array,byteOffset);}else{array=new Uint8Array(array,byteOffset,length);}if(Buffer.TYPED_ARRAY_SUPPORT){// Return an augmented `Uint8Array` instance, for best performance
that=array;that.__proto__=Buffer.prototype;}else{// Fallback: Return an object instance of the Buffer class
that=fromArrayLike(that,array);}return that;}function fromObject(that,obj){if(Buffer.isBuffer(obj)){var len=checked(obj.length)|0;that=createBuffer(that,len);if(that.length===0){return that;}obj.copy(that,0,0,len);return that;}if(obj){if(typeof ArrayBuffer!=='undefined'&&obj.buffer instanceof ArrayBuffer||'length'in obj){if(typeof obj.length!=='number'||isnan(obj.length)){return createBuffer(that,0);}return fromArrayLike(that,obj);}if(obj.type==='Buffer'&&isArray(obj.data)){return fromArrayLike(that,obj.data);}}throw new TypeError('First argument must be a string, Buffer, ArrayBuffer, Array, or array-like object.');}function checked(length){// Note: cannot use `length < kMaxLength()` here because that fails when
// length is NaN (which is otherwise coerced to zero.)
if(length>=kMaxLength()){throw new RangeError('Attempt to allocate Buffer larger than maximum '+'size: 0x'+kMaxLength().toString(16)+' bytes');}return length|0;}function SlowBuffer(length){if(+length!=length){// eslint-disable-line eqeqeq
length=0;}return Buffer.alloc(+length);}Buffer.isBuffer=function isBuffer(b){return!!(b!=null&&b._isBuffer);};Buffer.compare=function compare(a,b){if(!Buffer.isBuffer(a)||!Buffer.isBuffer(b)){throw new TypeError('Arguments must be Buffers');}if(a===b)return 0;var x=a.length;var y=b.length;for(var i=0,len=Math.min(x,y);i<len;++i){if(a[i]!==b[i]){x=a[i];y=b[i];break;}}if(x<y)return-1;if(y<x)return 1;return 0;};Buffer.isEncoding=function isEncoding(encoding){switch(String(encoding).toLowerCase()){case'hex':case'utf8':case'utf-8':case'ascii':case'latin1':case'binary':case'base64':case'ucs2':case'ucs-2':case'utf16le':case'utf-16le':return true;default:return false;}};Buffer.concat=function concat(list,length){if(!isArray(list)){throw new TypeError('"list" argument must be an Array of Buffers');}if(list.length===0){return Buffer.alloc(0);}var i;if(length===undefined){length=0;for(i=0;i<list.length;++i){length+=list[i].length;}}var buffer=Buffer.allocUnsafe(length);var pos=0;for(i=0;i<list.length;++i){var buf=list[i];if(!Buffer.isBuffer(buf)){throw new TypeError('"list" argument must be an Array of Buffers');}buf.copy(buffer,pos);pos+=buf.length;}return buffer;};function byteLength(string,encoding){if(Buffer.isBuffer(string)){return string.length;}if(typeof ArrayBuffer!=='undefined'&&typeof ArrayBuffer.isView==='function'&&(ArrayBuffer.isView(string)||string instanceof ArrayBuffer)){return string.byteLength;}if(typeof string!=='string'){string=''+string;}var len=string.length;if(len===0)return 0;// Use a for loop to avoid recursion
var loweredCase=false;for(;;){switch(encoding){case'ascii':case'latin1':case'binary':return len;case'utf8':case'utf-8':case undefined:return utf8ToBytes(string).length;case'ucs2':case'ucs-2':case'utf16le':case'utf-16le':return len*2;case'hex':return len>>>1;case'base64':return base64ToBytes(string).length;default:if(loweredCase)return utf8ToBytes(string).length;// assume utf8
encoding=(''+encoding).toLowerCase();loweredCase=true;}}}Buffer.byteLength=byteLength;function slowToString(encoding,start,end){var loweredCase=false;// No need to verify that "this.length <= MAX_UINT32" since it's a read-only
// property of a typed array.
// This behaves neither like String nor Uint8Array in that we set start/end
// to their upper/lower bounds if the value passed is out of range.
// undefined is handled specially as per ECMA-262 6th Edition,
// Section 13.3.3.7 Runtime Semantics: KeyedBindingInitialization.
if(start===undefined||start<0){start=0;}// Return early if start > this.length. Done here to prevent potential uint32
// coercion fail below.
if(start>this.length){return'';}if(end===undefined||end>this.length){end=this.length;}if(end<=0){return'';}// Force coersion to uint32. This will also coerce falsey/NaN values to 0.
end>>>=0;start>>>=0;if(end<=start){return'';}if(!encoding)encoding='utf8';while(true){switch(encoding){case'hex':return hexSlice(this,start,end);case'utf8':case'utf-8':return utf8Slice(this,start,end);case'ascii':return asciiSlice(this,start,end);case'latin1':case'binary':return latin1Slice(this,start,end);case'base64':return base64Slice(this,start,end);case'ucs2':case'ucs-2':case'utf16le':case'utf-16le':return utf16leSlice(this,start,end);default:if(loweredCase)throw new TypeError('Unknown encoding: '+encoding);encoding=(encoding+'').toLowerCase();loweredCase=true;}}}// The property is used by `Buffer.isBuffer` and `is-buffer` (in Safari 5-7) to detect
// Buffer instances.
Buffer.prototype._isBuffer=true;function swap(b,n,m){var i=b[n];b[n]=b[m];b[m]=i;}Buffer.prototype.swap16=function swap16(){var len=this.length;if(len%2!==0){throw new RangeError('Buffer size must be a multiple of 16-bits');}for(var i=0;i<len;i+=2){swap(this,i,i+1);}return this;};Buffer.prototype.swap32=function swap32(){var len=this.length;if(len%4!==0){throw new RangeError('Buffer size must be a multiple of 32-bits');}for(var i=0;i<len;i+=4){swap(this,i,i+3);swap(this,i+1,i+2);}return this;};Buffer.prototype.swap64=function swap64(){var len=this.length;if(len%8!==0){throw new RangeError('Buffer size must be a multiple of 64-bits');}for(var i=0;i<len;i+=8){swap(this,i,i+7);swap(this,i+1,i+6);swap(this,i+2,i+5);swap(this,i+3,i+4);}return this;};Buffer.prototype.toString=function toString(){var length=this.length|0;if(length===0)return'';if(arguments.length===0)return utf8Slice(this,0,length);return slowToString.apply(this,arguments);};Buffer.prototype.equals=function equals(b){if(!Buffer.isBuffer(b))throw new TypeError('Argument must be a Buffer');if(this===b)return true;return Buffer.compare(this,b)===0;};Buffer.prototype.inspect=function inspect(){var str='';var max=exports.INSPECT_MAX_BYTES;if(this.length>0){str=this.toString('hex',0,max).match(/.{2}/g).join(' ');if(this.length>max)str+=' ... ';}return'<Buffer '+str+'>';};Buffer.prototype.compare=function compare(target,start,end,thisStart,thisEnd){if(!Buffer.isBuffer(target)){throw new TypeError('Argument must be a Buffer');}if(start===undefined){start=0;}if(end===undefined){end=target?target.length:0;}if(thisStart===undefined){thisStart=0;}if(thisEnd===undefined){thisEnd=this.length;}if(start<0||end>target.length||thisStart<0||thisEnd>this.length){throw new RangeError('out of range index');}if(thisStart>=thisEnd&&start>=end){return 0;}if(thisStart>=thisEnd){return-1;}if(start>=end){return 1;}start>>>=0;end>>>=0;thisStart>>>=0;thisEnd>>>=0;if(this===target)return 0;var x=thisEnd-thisStart;var y=end-start;var len=Math.min(x,y);var thisCopy=this.slice(thisStart,thisEnd);var targetCopy=target.slice(start,end);for(var i=0;i<len;++i){if(thisCopy[i]!==targetCopy[i]){x=thisCopy[i];y=targetCopy[i];break;}}if(x<y)return-1;if(y<x)return 1;return 0;};// Finds either the first index of `val` in `buffer` at offset >= `byteOffset`,
// OR the last index of `val` in `buffer` at offset <= `byteOffset`.
//
// Arguments:
// - buffer - a Buffer to search
// - val - a string, Buffer, or number
// - byteOffset - an index into `buffer`; will be clamped to an int32
// - encoding - an optional encoding, relevant is val is a string
// - dir - true for indexOf, false for lastIndexOf
function bidirectionalIndexOf(buffer,val,byteOffset,encoding,dir){// Empty buffer means no match
if(buffer.length===0)return-1;// Normalize byteOffset
if(typeof byteOffset==='string'){encoding=byteOffset;byteOffset=0;}else if(byteOffset>0x7fffffff){byteOffset=0x7fffffff;}else if(byteOffset<-0x80000000){byteOffset=-0x80000000;}byteOffset=+byteOffset;// Coerce to Number.
if(isNaN(byteOffset)){// byteOffset: it it's undefined, null, NaN, "foo", etc, search whole buffer
byteOffset=dir?0:buffer.length-1;}// Normalize byteOffset: negative offsets start from the end of the buffer
if(byteOffset<0)byteOffset=buffer.length+byteOffset;if(byteOffset>=buffer.length){if(dir)return-1;else byteOffset=buffer.length-1;}else if(byteOffset<0){if(dir)byteOffset=0;else return-1;}// Normalize val
if(typeof val==='string'){val=Buffer.from(val,encoding);}// Finally, search either indexOf (if dir is true) or lastIndexOf
if(Buffer.isBuffer(val)){// Special case: looking for empty string/buffer always fails
if(val.length===0){return-1;}return arrayIndexOf(buffer,val,byteOffset,encoding,dir);}else if(typeof val==='number'){val=val&0xFF;// Search for a byte value [0-255]
if(Buffer.TYPED_ARRAY_SUPPORT&&typeof Uint8Array.prototype.indexOf==='function'){if(dir){return Uint8Array.prototype.indexOf.call(buffer,val,byteOffset);}else{return Uint8Array.prototype.lastIndexOf.call(buffer,val,byteOffset);}}return arrayIndexOf(buffer,[val],byteOffset,encoding,dir);}throw new TypeError('val must be string, number or Buffer');}function arrayIndexOf(arr,val,byteOffset,encoding,dir){var indexSize=1;var arrLength=arr.length;var valLength=val.length;if(encoding!==undefined){encoding=String(encoding).toLowerCase();if(encoding==='ucs2'||encoding==='ucs-2'||encoding==='utf16le'||encoding==='utf-16le'){if(arr.length<2||val.length<2){return-1;}indexSize=2;arrLength/=2;valLength/=2;byteOffset/=2;}}function read(buf,i){if(indexSize===1){return buf[i];}else{return buf.readUInt16BE(i*indexSize);}}var i;if(dir){var foundIndex=-1;for(i=byteOffset;i<arrLength;i++){if(read(arr,i)===read(val,foundIndex===-1?0:i-foundIndex)){if(foundIndex===-1)foundIndex=i;if(i-foundIndex+1===valLength)return foundIndex*indexSize;}else{if(foundIndex!==-1)i-=i-foundIndex;foundIndex=-1;}}}else{if(byteOffset+valLength>arrLength)byteOffset=arrLength-valLength;for(i=byteOffset;i>=0;i--){var found=true;for(var j=0;j<valLength;j++){if(read(arr,i+j)!==read(val,j)){found=false;break;}}if(found)return i;}}return-1;}Buffer.prototype.includes=function includes(val,byteOffset,encoding){return this.indexOf(val,byteOffset,encoding)!==-1;};Buffer.prototype.indexOf=function indexOf(val,byteOffset,encoding){return bidirectionalIndexOf(this,val,byteOffset,encoding,true);};Buffer.prototype.lastIndexOf=function lastIndexOf(val,byteOffset,encoding){return bidirectionalIndexOf(this,val,byteOffset,encoding,false);};function hexWrite(buf,string,offset,length){offset=Number(offset)||0;var remaining=buf.length-offset;if(!length){length=remaining;}else{length=Number(length);if(length>remaining){length=remaining;}}// must be an even number of digits
var strLen=string.length;if(strLen%2!==0)throw new TypeError('Invalid hex string');if(length>strLen/2){length=strLen/2;}for(var i=0;i<length;++i){var parsed=parseInt(string.substr(i*2,2),16);if(isNaN(parsed))return i;buf[offset+i]=parsed;}return i;}function utf8Write(buf,string,offset,length){return blitBuffer(utf8ToBytes(string,buf.length-offset),buf,offset,length);}function asciiWrite(buf,string,offset,length){return blitBuffer(asciiToBytes(string),buf,offset,length);}function latin1Write(buf,string,offset,length){return asciiWrite(buf,string,offset,length);}function base64Write(buf,string,offset,length){return blitBuffer(base64ToBytes(string),buf,offset,length);}function ucs2Write(buf,string,offset,length){return blitBuffer(utf16leToBytes(string,buf.length-offset),buf,offset,length);}Buffer.prototype.write=function write(string,offset,length,encoding){// Buffer#write(string)
if(offset===undefined){encoding='utf8';length=this.length;offset=0;// Buffer#write(string, encoding)
}else if(length===undefined&&typeof offset==='string'){encoding=offset;length=this.length;offset=0;// Buffer#write(string, offset[, length][, encoding])
}else if(isFinite(offset)){offset=offset|0;if(isFinite(length)){length=length|0;if(encoding===undefined)encoding='utf8';}else{encoding=length;length=undefined;}// legacy write(string, encoding, offset, length) - remove in v0.13
}else{throw new Error('Buffer.write(string, encoding, offset[, length]) is no longer supported');}var remaining=this.length-offset;if(length===undefined||length>remaining)length=remaining;if(string.length>0&&(length<0||offset<0)||offset>this.length){throw new RangeError('Attempt to write outside buffer bounds');}if(!encoding)encoding='utf8';var loweredCase=false;for(;;){switch(encoding){case'hex':return hexWrite(this,string,offset,length);case'utf8':case'utf-8':return utf8Write(this,string,offset,length);case'ascii':return asciiWrite(this,string,offset,length);case'latin1':case'binary':return latin1Write(this,string,offset,length);case'base64':// Warning: maxLength not taken into account in base64Write
return base64Write(this,string,offset,length);case'ucs2':case'ucs-2':case'utf16le':case'utf-16le':return ucs2Write(this,string,offset,length);default:if(loweredCase)throw new TypeError('Unknown encoding: '+encoding);encoding=(''+encoding).toLowerCase();loweredCase=true;}}};Buffer.prototype.toJSON=function toJSON(){return{type:'Buffer',data:Array.prototype.slice.call(this._arr||this,0)};};function base64Slice(buf,start,end){if(start===0&&end===buf.length){return base64.fromByteArray(buf);}else{return base64.fromByteArray(buf.slice(start,end));}}function utf8Slice(buf,start,end){end=Math.min(buf.length,end);var res=[];var i=start;while(i<end){var firstByte=buf[i];var codePoint=null;var bytesPerSequence=firstByte>0xEF?4:firstByte>0xDF?3:firstByte>0xBF?2:1;if(i+bytesPerSequence<=end){var secondByte,thirdByte,fourthByte,tempCodePoint;switch(bytesPerSequence){case 1:if(firstByte<0x80){codePoint=firstByte;}break;case 2:secondByte=buf[i+1];if((secondByte&0xC0)===0x80){tempCodePoint=(firstByte&0x1F)<<0x6|secondByte&0x3F;if(tempCodePoint>0x7F){codePoint=tempCodePoint;}}break;case 3:secondByte=buf[i+1];thirdByte=buf[i+2];if((secondByte&0xC0)===0x80&&(thirdByte&0xC0)===0x80){tempCodePoint=(firstByte&0xF)<<0xC|(secondByte&0x3F)<<0x6|thirdByte&0x3F;if(tempCodePoint>0x7FF&&(tempCodePoint<0xD800||tempCodePoint>0xDFFF)){codePoint=tempCodePoint;}}break;case 4:secondByte=buf[i+1];thirdByte=buf[i+2];fourthByte=buf[i+3];if((secondByte&0xC0)===0x80&&(thirdByte&0xC0)===0x80&&(fourthByte&0xC0)===0x80){tempCodePoint=(firstByte&0xF)<<0x12|(secondByte&0x3F)<<0xC|(thirdByte&0x3F)<<0x6|fourthByte&0x3F;if(tempCodePoint>0xFFFF&&tempCodePoint<0x110000){codePoint=tempCodePoint;}}}}if(codePoint===null){// we did not generate a valid codePoint so insert a
// replacement char (U+FFFD) and advance only 1 byte
codePoint=0xFFFD;bytesPerSequence=1;}else if(codePoint>0xFFFF){// encode to utf16 (surrogate pair dance)
codePoint-=0x10000;res.push(codePoint>>>10&0x3FF|0xD800);codePoint=0xDC00|codePoint&0x3FF;}res.push(codePoint);i+=bytesPerSequence;}return decodeCodePointsArray(res);}// Based on http://stackoverflow.com/a/22747272/680742, the browser with
// the lowest limit is Chrome, with 0x10000 args.
// We go 1 magnitude less, for safety
var MAX_ARGUMENTS_LENGTH=0x1000;function decodeCodePointsArray(codePoints){var len=codePoints.length;if(len<=MAX_ARGUMENTS_LENGTH){return String.fromCharCode.apply(String,codePoints);// avoid extra slice()
}// Decode in chunks to avoid "call stack size exceeded".
var res='';var i=0;while(i<len){res+=String.fromCharCode.apply(String,codePoints.slice(i,i+=MAX_ARGUMENTS_LENGTH));}return res;}function asciiSlice(buf,start,end){var ret='';end=Math.min(buf.length,end);for(var i=start;i<end;++i){ret+=String.fromCharCode(buf[i]&0x7F);}return ret;}function latin1Slice(buf,start,end){var ret='';end=Math.min(buf.length,end);for(var i=start;i<end;++i){ret+=String.fromCharCode(buf[i]);}return ret;}function hexSlice(buf,start,end){var len=buf.length;if(!start||start<0)start=0;if(!end||end<0||end>len)end=len;var out='';for(var i=start;i<end;++i){out+=toHex(buf[i]);}return out;}function utf16leSlice(buf,start,end){var bytes=buf.slice(start,end);var res='';for(var i=0;i<bytes.length;i+=2){res+=String.fromCharCode(bytes[i]+bytes[i+1]*256);}return res;}Buffer.prototype.slice=function slice(start,end){var len=this.length;start=~~start;end=end===undefined?len:~~end;if(start<0){start+=len;if(start<0)start=0;}else if(start>len){start=len;}if(end<0){end+=len;if(end<0)end=0;}else if(end>len){end=len;}if(end<start)end=start;var newBuf;if(Buffer.TYPED_ARRAY_SUPPORT){newBuf=this.subarray(start,end);newBuf.__proto__=Buffer.prototype;}else{var sliceLen=end-start;newBuf=new Buffer(sliceLen,undefined);for(var i=0;i<sliceLen;++i){newBuf[i]=this[i+start];}}return newBuf;};/*
 * Need to make sure that buffer isn't trying to write out of bounds.
 */function checkOffset(offset,ext,length){if(offset%1!==0||offset<0)throw new RangeError('offset is not uint');if(offset+ext>length)throw new RangeError('Trying to access beyond buffer length');}Buffer.prototype.readUIntLE=function readUIntLE(offset,byteLength,noAssert){offset=offset|0;byteLength=byteLength|0;if(!noAssert)checkOffset(offset,byteLength,this.length);var val=this[offset];var mul=1;var i=0;while(++i<byteLength&&(mul*=0x100)){val+=this[offset+i]*mul;}return val;};Buffer.prototype.readUIntBE=function readUIntBE(offset,byteLength,noAssert){offset=offset|0;byteLength=byteLength|0;if(!noAssert){checkOffset(offset,byteLength,this.length);}var val=this[offset+--byteLength];var mul=1;while(byteLength>0&&(mul*=0x100)){val+=this[offset+--byteLength]*mul;}return val;};Buffer.prototype.readUInt8=function readUInt8(offset,noAssert){if(!noAssert)checkOffset(offset,1,this.length);return this[offset];};Buffer.prototype.readUInt16LE=function readUInt16LE(offset,noAssert){if(!noAssert)checkOffset(offset,2,this.length);return this[offset]|this[offset+1]<<8;};Buffer.prototype.readUInt16BE=function readUInt16BE(offset,noAssert){if(!noAssert)checkOffset(offset,2,this.length);return this[offset]<<8|this[offset+1];};Buffer.prototype.readUInt32LE=function readUInt32LE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return(this[offset]|this[offset+1]<<8|this[offset+2]<<16)+this[offset+3]*0x1000000;};Buffer.prototype.readUInt32BE=function readUInt32BE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return this[offset]*0x1000000+(this[offset+1]<<16|this[offset+2]<<8|this[offset+3]);};Buffer.prototype.readIntLE=function readIntLE(offset,byteLength,noAssert){offset=offset|0;byteLength=byteLength|0;if(!noAssert)checkOffset(offset,byteLength,this.length);var val=this[offset];var mul=1;var i=0;while(++i<byteLength&&(mul*=0x100)){val+=this[offset+i]*mul;}mul*=0x80;if(val>=mul)val-=Math.pow(2,8*byteLength);return val;};Buffer.prototype.readIntBE=function readIntBE(offset,byteLength,noAssert){offset=offset|0;byteLength=byteLength|0;if(!noAssert)checkOffset(offset,byteLength,this.length);var i=byteLength;var mul=1;var val=this[offset+--i];while(i>0&&(mul*=0x100)){val+=this[offset+--i]*mul;}mul*=0x80;if(val>=mul)val-=Math.pow(2,8*byteLength);return val;};Buffer.prototype.readInt8=function readInt8(offset,noAssert){if(!noAssert)checkOffset(offset,1,this.length);if(!(this[offset]&0x80))return this[offset];return(0xff-this[offset]+1)*-1;};Buffer.prototype.readInt16LE=function readInt16LE(offset,noAssert){if(!noAssert)checkOffset(offset,2,this.length);var val=this[offset]|this[offset+1]<<8;return val&0x8000?val|0xFFFF0000:val;};Buffer.prototype.readInt16BE=function readInt16BE(offset,noAssert){if(!noAssert)checkOffset(offset,2,this.length);var val=this[offset+1]|this[offset]<<8;return val&0x8000?val|0xFFFF0000:val;};Buffer.prototype.readInt32LE=function readInt32LE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return this[offset]|this[offset+1]<<8|this[offset+2]<<16|this[offset+3]<<24;};Buffer.prototype.readInt32BE=function readInt32BE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return this[offset]<<24|this[offset+1]<<16|this[offset+2]<<8|this[offset+3];};Buffer.prototype.readFloatLE=function readFloatLE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return ieee754.read(this,offset,true,23,4);};Buffer.prototype.readFloatBE=function readFloatBE(offset,noAssert){if(!noAssert)checkOffset(offset,4,this.length);return ieee754.read(this,offset,false,23,4);};Buffer.prototype.readDoubleLE=function readDoubleLE(offset,noAssert){if(!noAssert)checkOffset(offset,8,this.length);return ieee754.read(this,offset,true,52,8);};Buffer.prototype.readDoubleBE=function readDoubleBE(offset,noAssert){if(!noAssert)checkOffset(offset,8,this.length);return ieee754.read(this,offset,false,52,8);};function checkInt(buf,value,offset,ext,max,min){if(!Buffer.isBuffer(buf))throw new TypeError('"buffer" argument must be a Buffer instance');if(value>max||value<min)throw new RangeError('"value" argument is out of bounds');if(offset+ext>buf.length)throw new RangeError('Index out of range');}Buffer.prototype.writeUIntLE=function writeUIntLE(value,offset,byteLength,noAssert){value=+value;offset=offset|0;byteLength=byteLength|0;if(!noAssert){var maxBytes=Math.pow(2,8*byteLength)-1;checkInt(this,value,offset,byteLength,maxBytes,0);}var mul=1;var i=0;this[offset]=value&0xFF;while(++i<byteLength&&(mul*=0x100)){this[offset+i]=value/mul&0xFF;}return offset+byteLength;};Buffer.prototype.writeUIntBE=function writeUIntBE(value,offset,byteLength,noAssert){value=+value;offset=offset|0;byteLength=byteLength|0;if(!noAssert){var maxBytes=Math.pow(2,8*byteLength)-1;checkInt(this,value,offset,byteLength,maxBytes,0);}var i=byteLength-1;var mul=1;this[offset+i]=value&0xFF;while(--i>=0&&(mul*=0x100)){this[offset+i]=value/mul&0xFF;}return offset+byteLength;};Buffer.prototype.writeUInt8=function writeUInt8(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,1,0xff,0);if(!Buffer.TYPED_ARRAY_SUPPORT)value=Math.floor(value);this[offset]=value&0xff;return offset+1;};function objectWriteUInt16(buf,value,offset,littleEndian){if(value<0)value=0xffff+value+1;for(var i=0,j=Math.min(buf.length-offset,2);i<j;++i){buf[offset+i]=(value&0xff<<8*(littleEndian?i:1-i))>>>(littleEndian?i:1-i)*8;}}Buffer.prototype.writeUInt16LE=function writeUInt16LE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,2,0xffff,0);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value&0xff;this[offset+1]=value>>>8;}else{objectWriteUInt16(this,value,offset,true);}return offset+2;};Buffer.prototype.writeUInt16BE=function writeUInt16BE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,2,0xffff,0);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value>>>8;this[offset+1]=value&0xff;}else{objectWriteUInt16(this,value,offset,false);}return offset+2;};function objectWriteUInt32(buf,value,offset,littleEndian){if(value<0)value=0xffffffff+value+1;for(var i=0,j=Math.min(buf.length-offset,4);i<j;++i){buf[offset+i]=value>>>(littleEndian?i:3-i)*8&0xff;}}Buffer.prototype.writeUInt32LE=function writeUInt32LE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,4,0xffffffff,0);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset+3]=value>>>24;this[offset+2]=value>>>16;this[offset+1]=value>>>8;this[offset]=value&0xff;}else{objectWriteUInt32(this,value,offset,true);}return offset+4;};Buffer.prototype.writeUInt32BE=function writeUInt32BE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,4,0xffffffff,0);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value>>>24;this[offset+1]=value>>>16;this[offset+2]=value>>>8;this[offset+3]=value&0xff;}else{objectWriteUInt32(this,value,offset,false);}return offset+4;};Buffer.prototype.writeIntLE=function writeIntLE(value,offset,byteLength,noAssert){value=+value;offset=offset|0;if(!noAssert){var limit=Math.pow(2,8*byteLength-1);checkInt(this,value,offset,byteLength,limit-1,-limit);}var i=0;var mul=1;var sub=0;this[offset]=value&0xFF;while(++i<byteLength&&(mul*=0x100)){if(value<0&&sub===0&&this[offset+i-1]!==0){sub=1;}this[offset+i]=(value/mul>>0)-sub&0xFF;}return offset+byteLength;};Buffer.prototype.writeIntBE=function writeIntBE(value,offset,byteLength,noAssert){value=+value;offset=offset|0;if(!noAssert){var limit=Math.pow(2,8*byteLength-1);checkInt(this,value,offset,byteLength,limit-1,-limit);}var i=byteLength-1;var mul=1;var sub=0;this[offset+i]=value&0xFF;while(--i>=0&&(mul*=0x100)){if(value<0&&sub===0&&this[offset+i+1]!==0){sub=1;}this[offset+i]=(value/mul>>0)-sub&0xFF;}return offset+byteLength;};Buffer.prototype.writeInt8=function writeInt8(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,1,0x7f,-0x80);if(!Buffer.TYPED_ARRAY_SUPPORT)value=Math.floor(value);if(value<0)value=0xff+value+1;this[offset]=value&0xff;return offset+1;};Buffer.prototype.writeInt16LE=function writeInt16LE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,2,0x7fff,-0x8000);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value&0xff;this[offset+1]=value>>>8;}else{objectWriteUInt16(this,value,offset,true);}return offset+2;};Buffer.prototype.writeInt16BE=function writeInt16BE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,2,0x7fff,-0x8000);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value>>>8;this[offset+1]=value&0xff;}else{objectWriteUInt16(this,value,offset,false);}return offset+2;};Buffer.prototype.writeInt32LE=function writeInt32LE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,4,0x7fffffff,-0x80000000);if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value&0xff;this[offset+1]=value>>>8;this[offset+2]=value>>>16;this[offset+3]=value>>>24;}else{objectWriteUInt32(this,value,offset,true);}return offset+4;};Buffer.prototype.writeInt32BE=function writeInt32BE(value,offset,noAssert){value=+value;offset=offset|0;if(!noAssert)checkInt(this,value,offset,4,0x7fffffff,-0x80000000);if(value<0)value=0xffffffff+value+1;if(Buffer.TYPED_ARRAY_SUPPORT){this[offset]=value>>>24;this[offset+1]=value>>>16;this[offset+2]=value>>>8;this[offset+3]=value&0xff;}else{objectWriteUInt32(this,value,offset,false);}return offset+4;};function checkIEEE754(buf,value,offset,ext,max,min){if(offset+ext>buf.length)throw new RangeError('Index out of range');if(offset<0)throw new RangeError('Index out of range');}function writeFloat(buf,value,offset,littleEndian,noAssert){if(!noAssert){checkIEEE754(buf,value,offset,4,3.4028234663852886e+38,-3.4028234663852886e+38);}ieee754.write(buf,value,offset,littleEndian,23,4);return offset+4;}Buffer.prototype.writeFloatLE=function writeFloatLE(value,offset,noAssert){return writeFloat(this,value,offset,true,noAssert);};Buffer.prototype.writeFloatBE=function writeFloatBE(value,offset,noAssert){return writeFloat(this,value,offset,false,noAssert);};function writeDouble(buf,value,offset,littleEndian,noAssert){if(!noAssert){checkIEEE754(buf,value,offset,8,1.7976931348623157E+308,-1.7976931348623157E+308);}ieee754.write(buf,value,offset,littleEndian,52,8);return offset+8;}Buffer.prototype.writeDoubleLE=function writeDoubleLE(value,offset,noAssert){return writeDouble(this,value,offset,true,noAssert);};Buffer.prototype.writeDoubleBE=function writeDoubleBE(value,offset,noAssert){return writeDouble(this,value,offset,false,noAssert);};// copy(targetBuffer, targetStart=0, sourceStart=0, sourceEnd=buffer.length)
Buffer.prototype.copy=function copy(target,targetStart,start,end){if(!start)start=0;if(!end&&end!==0)end=this.length;if(targetStart>=target.length)targetStart=target.length;if(!targetStart)targetStart=0;if(end>0&&end<start)end=start;// Copy 0 bytes; we're done
if(end===start)return 0;if(target.length===0||this.length===0)return 0;// Fatal error conditions
if(targetStart<0){throw new RangeError('targetStart out of bounds');}if(start<0||start>=this.length)throw new RangeError('sourceStart out of bounds');if(end<0)throw new RangeError('sourceEnd out of bounds');// Are we oob?
if(end>this.length)end=this.length;if(target.length-targetStart<end-start){end=target.length-targetStart+start;}var len=end-start;var i;if(this===target&&start<targetStart&&targetStart<end){// descending copy from end
for(i=len-1;i>=0;--i){target[i+targetStart]=this[i+start];}}else if(len<1000||!Buffer.TYPED_ARRAY_SUPPORT){// ascending copy from start
for(i=0;i<len;++i){target[i+targetStart]=this[i+start];}}else{Uint8Array.prototype.set.call(target,this.subarray(start,start+len),targetStart);}return len;};// Usage:
//    buffer.fill(number[, offset[, end]])
//    buffer.fill(buffer[, offset[, end]])
//    buffer.fill(string[, offset[, end]][, encoding])
Buffer.prototype.fill=function fill(val,start,end,encoding){// Handle string cases:
if(typeof val==='string'){if(typeof start==='string'){encoding=start;start=0;end=this.length;}else if(typeof end==='string'){encoding=end;end=this.length;}if(val.length===1){var code=val.charCodeAt(0);if(code<256){val=code;}}if(encoding!==undefined&&typeof encoding!=='string'){throw new TypeError('encoding must be a string');}if(typeof encoding==='string'&&!Buffer.isEncoding(encoding)){throw new TypeError('Unknown encoding: '+encoding);}}else if(typeof val==='number'){val=val&255;}// Invalid ranges are not set to a default, so can range check early.
if(start<0||this.length<start||this.length<end){throw new RangeError('Out of range index');}if(end<=start){return this;}start=start>>>0;end=end===undefined?this.length:end>>>0;if(!val)val=0;var i;if(typeof val==='number'){for(i=start;i<end;++i){this[i]=val;}}else{var bytes=Buffer.isBuffer(val)?val:utf8ToBytes(new Buffer(val,encoding).toString());var len=bytes.length;for(i=0;i<end-start;++i){this[i+start]=bytes[i%len];}}return this;};// HELPER FUNCTIONS
// ================
var INVALID_BASE64_RE=/[^+\/0-9A-Za-z-_]/g;function base64clean(str){// Node strips out invalid characters like \n and \t from the string, base64-js does not
str=stringtrim(str).replace(INVALID_BASE64_RE,'');// Node converts strings with length < 2 to ''
if(str.length<2)return'';// Node allows for non-padded base64 strings (missing trailing ===), base64-js does not
while(str.length%4!==0){str=str+'=';}return str;}function stringtrim(str){if(str.trim)return str.trim();return str.replace(/^\s+|\s+$/g,'');}function toHex(n){if(n<16)return'0'+n.toString(16);return n.toString(16);}function utf8ToBytes(string,units){units=units||Infinity;var codePoint;var length=string.length;var leadSurrogate=null;var bytes=[];for(var i=0;i<length;++i){codePoint=string.charCodeAt(i);// is surrogate component
if(codePoint>0xD7FF&&codePoint<0xE000){// last char was a lead
if(!leadSurrogate){// no lead yet
if(codePoint>0xDBFF){// unexpected trail
if((units-=3)>-1)bytes.push(0xEF,0xBF,0xBD);continue;}else if(i+1===length){// unpaired lead
if((units-=3)>-1)bytes.push(0xEF,0xBF,0xBD);continue;}// valid lead
leadSurrogate=codePoint;continue;}// 2 leads in a row
if(codePoint<0xDC00){if((units-=3)>-1)bytes.push(0xEF,0xBF,0xBD);leadSurrogate=codePoint;continue;}// valid surrogate pair
codePoint=(leadSurrogate-0xD800<<10|codePoint-0xDC00)+0x10000;}else if(leadSurrogate){// valid bmp char, but last char was a lead
if((units-=3)>-1)bytes.push(0xEF,0xBF,0xBD);}leadSurrogate=null;// encode utf8
if(codePoint<0x80){if((units-=1)<0)break;bytes.push(codePoint);}else if(codePoint<0x800){if((units-=2)<0)break;bytes.push(codePoint>>0x6|0xC0,codePoint&0x3F|0x80);}else if(codePoint<0x10000){if((units-=3)<0)break;bytes.push(codePoint>>0xC|0xE0,codePoint>>0x6&0x3F|0x80,codePoint&0x3F|0x80);}else if(codePoint<0x110000){if((units-=4)<0)break;bytes.push(codePoint>>0x12|0xF0,codePoint>>0xC&0x3F|0x80,codePoint>>0x6&0x3F|0x80,codePoint&0x3F|0x80);}else{throw new Error('Invalid code point');}}return bytes;}function asciiToBytes(str){var byteArray=[];for(var i=0;i<str.length;++i){// Node's code seems to be doing this and not & 0x7F..
byteArray.push(str.charCodeAt(i)&0xFF);}return byteArray;}function utf16leToBytes(str,units){var c,hi,lo;var byteArray=[];for(var i=0;i<str.length;++i){if((units-=2)<0)break;c=str.charCodeAt(i);hi=c>>8;lo=c%256;byteArray.push(lo);byteArray.push(hi);}return byteArray;}function base64ToBytes(str){return base64.toByteArray(base64clean(str));}function blitBuffer(src,dst,offset,length){for(var i=0;i<length;++i){if(i+offset>=dst.length||i>=src.length)break;dst[i+offset]=src[i];}return i;}function isnan(val){return val!==val;// eslint-disable-line no-self-compare
}
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(12).Buffer, __webpack_require__(18)))

/***/ },
/* 13 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return this.multiple?'[]':'';};

/***/ },
/* 14 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(e){var _this=this;if(this.lazy&&e.type=='keyup')return;if(!this.lazy&&e.type=='change')return;if(this.lazy&&e.type=='change'){this.curValue=e.target.value;this.validateRemote();}if(!this.lazy&&e.type=='keyup'){this.lastKeyStroke=Date.now();setTimeout(function(){var elapsed=Date.now()-_this.lastKeyStroke;if(elapsed>=_this.debounce){_this.curValue=e.target.value;_this.validateRemote();}},this.debounce);}};

/***/ },
/* 15 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function isNumeric(n){return!isNaN(parseFloat(n))&&isFinite(n);};

/***/ },
/* 16 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that,trigger,values){if(!trigger)return true;var triggerValue=trigger.curValue;if(!values)return!!triggerValue;if(triggerValue==null)return false;values=values.split(",");var value=(typeof triggerValue==='undefined'?'undefined':_typeof(triggerValue))=='object'?triggerValue:[triggerValue];return!!values.filter(function(n){value=''+value;return value.indexOf(n)!=-1;}).length;};

/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
"use strict";var isTriggerOn=__webpack_require__(16);module.exports=function(that,rule){if(!that.inForm())return true;var params=that.Rules[rule].split(":");var triggerName=params[0];var values=params.length>1?params[1]:false;var trigger=that.getField(triggerName);var required=isTriggerOn(that,trigger,values);that.isRequired=required;return required;};

/***/ },
/* 18 */
/***/ function(module, exports) {

"use strict";
"use strict";var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};var g;// This works in non-strict mode
g=function(){return this;}();try{// This works if eval is allowed (see CSP)
g=g||Function("return this")()||(1,eval)("this");}catch(e){// This works if the window reference is available
if((typeof window==="undefined"?"undefined":_typeof(window))==="object")g=window;}// g can still be undefined, but nothing to do about it...
// We return undefined, instead of nothing here, so it's
// easier to handle this case. if(!global) { ...}
module.exports=g;

/***/ },
/* 19 */
/***/ function(module, exports) {

/* WEBPACK VAR INJECTION */(function(__webpack_amd_options__) {module.exports = __webpack_amd_options__;

/* WEBPACK VAR INJECTION */}.call(exports, {}))

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
/* WEBPACK VAR INJECTION */(function(global, module) {var __WEBPACK_AMD_DEFINE_RESULT__;'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};/**
 * @license
 * lodash (Custom Build) <https://lodash.com/>
 * Build: `lodash core -o ./dist/lodash.core.js`
 * Copyright JS Foundation and other contributors <https://js.foundation/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */;(function(){/** Used as a safe reference for `undefined` in pre-ES5 environments. */var undefined;/** Used as the semantic version number. */var VERSION='4.16.6';/** Error message constants. */var FUNC_ERROR_TEXT='Expected a function';/** Used to compose bitmasks for function metadata. */var BIND_FLAG=1,PARTIAL_FLAG=32;/** Used to compose bitmasks for comparison styles. */var UNORDERED_COMPARE_FLAG=1,PARTIAL_COMPARE_FLAG=2;/** Used as references for various `Number` constants. */var INFINITY=1/0,MAX_SAFE_INTEGER=9007199254740991;/** `Object#toString` result references. */var argsTag='[object Arguments]',arrayTag='[object Array]',asyncTag='[object AsyncFunction]',boolTag='[object Boolean]',dateTag='[object Date]',errorTag='[object Error]',funcTag='[object Function]',genTag='[object GeneratorFunction]',numberTag='[object Number]',objectTag='[object Object]',proxyTag='[object Proxy]',regexpTag='[object RegExp]',stringTag='[object String]';/** Used to match HTML entities and HTML characters. */var reUnescapedHtml=/[&<>"']/g,reHasUnescapedHtml=RegExp(reUnescapedHtml.source);/** Used to map characters to HTML entities. */var htmlEscapes={'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'};/** Detect free variable `global` from Node.js. */var freeGlobal=(typeof global==='undefined'?'undefined':_typeof(global))=='object'&&global&&global.Object===Object&&global;/** Detect free variable `self`. */var freeSelf=(typeof self==='undefined'?'undefined':_typeof(self))=='object'&&self&&self.Object===Object&&self;/** Used as a reference to the global object. */var root=freeGlobal||freeSelf||Function('return this')();/** Detect free variable `exports`. */var freeExports=( false?'undefined':_typeof(exports))=='object'&&exports&&!exports.nodeType&&exports;/** Detect free variable `module`. */var freeModule=freeExports&&( false?'undefined':_typeof(module))=='object'&&module&&!module.nodeType&&module;/*--------------------------------------------------------------------------*//**
   * Appends the elements of `values` to `array`.
   *
   * @private
   * @param {Array} array The array to modify.
   * @param {Array} values The values to append.
   * @returns {Array} Returns `array`.
   */function arrayPush(array,values){array.push.apply(array,values);return array;}/**
   * The base implementation of `_.findIndex` and `_.findLastIndex` without
   * support for iteratee shorthands.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {Function} predicate The function invoked per iteration.
   * @param {number} fromIndex The index to search from.
   * @param {boolean} [fromRight] Specify iterating from right to left.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */function baseFindIndex(array,predicate,fromIndex,fromRight){var length=array.length,index=fromIndex+(fromRight?1:-1);while(fromRight?index--:++index<length){if(predicate(array[index],index,array)){return index;}}return-1;}/**
   * The base implementation of `_.property` without support for deep paths.
   *
   * @private
   * @param {string} key The key of the property to get.
   * @returns {Function} Returns the new accessor function.
   */function baseProperty(key){return function(object){return object==null?undefined:object[key];};}/**
   * The base implementation of `_.propertyOf` without support for deep paths.
   *
   * @private
   * @param {Object} object The object to query.
   * @returns {Function} Returns the new accessor function.
   */function basePropertyOf(object){return function(key){return object==null?undefined:object[key];};}/**
   * The base implementation of `_.reduce` and `_.reduceRight`, without support
   * for iteratee shorthands, which iterates over `collection` using `eachFunc`.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @param {*} accumulator The initial value.
   * @param {boolean} initAccum Specify using the first or last element of
   *  `collection` as the initial value.
   * @param {Function} eachFunc The function to iterate over `collection`.
   * @returns {*} Returns the accumulated value.
   */function baseReduce(collection,iteratee,accumulator,initAccum,eachFunc){eachFunc(collection,function(value,index,collection){accumulator=initAccum?(initAccum=false,value):iteratee(accumulator,value,index,collection);});return accumulator;}/**
   * The base implementation of `_.values` and `_.valuesIn` which creates an
   * array of `object` property values corresponding to the property names
   * of `props`.
   *
   * @private
   * @param {Object} object The object to query.
   * @param {Array} props The property names to get values for.
   * @returns {Object} Returns the array of property values.
   */function baseValues(object,props){return baseMap(props,function(key){return object[key];});}/**
   * Used by `_.escape` to convert characters to HTML entities.
   *
   * @private
   * @param {string} chr The matched character to escape.
   * @returns {string} Returns the escaped character.
   */var escapeHtmlChar=basePropertyOf(htmlEscapes);/**
   * Creates a unary function that invokes `func` with its argument transformed.
   *
   * @private
   * @param {Function} func The function to wrap.
   * @param {Function} transform The argument transform.
   * @returns {Function} Returns the new function.
   */function overArg(func,transform){return function(arg){return func(transform(arg));};}/*--------------------------------------------------------------------------*//** Used for built-in method references. */var arrayProto=Array.prototype,objectProto=Object.prototype;/** Used to check objects for own properties. */var hasOwnProperty=objectProto.hasOwnProperty;/** Used to generate unique IDs. */var idCounter=0;/**
   * Used to resolve the
   * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
   * of values.
   */var nativeObjectToString=objectProto.toString;/** Used to restore the original `_` reference in `_.noConflict`. */var oldDash=root._;/** Built-in value references. */var objectCreate=Object.create,propertyIsEnumerable=objectProto.propertyIsEnumerable;/* Built-in method references for those with the same name as other `lodash` methods. */var nativeIsFinite=root.isFinite,nativeKeys=overArg(Object.keys,Object),nativeMax=Math.max;/*------------------------------------------------------------------------*//**
   * Creates a `lodash` object which wraps `value` to enable implicit method
   * chain sequences. Methods that operate on and return arrays, collections,
   * and functions can be chained together. Methods that retrieve a single value
   * or may return a primitive value will automatically end the chain sequence
   * and return the unwrapped value. Otherwise, the value must be unwrapped
   * with `_#value`.
   *
   * Explicit chain sequences, which must be unwrapped with `_#value`, may be
   * enabled using `_.chain`.
   *
   * The execution of chained methods is lazy, that is, it's deferred until
   * `_#value` is implicitly or explicitly called.
   *
   * Lazy evaluation allows several methods to support shortcut fusion.
   * Shortcut fusion is an optimization to merge iteratee calls; this avoids
   * the creation of intermediate arrays and can greatly reduce the number of
   * iteratee executions. Sections of a chain sequence qualify for shortcut
   * fusion if the section is applied to an array of at least `200` elements
   * and any iteratees accept only one argument. The heuristic for whether a
   * section qualifies for shortcut fusion is subject to change.
   *
   * Chaining is supported in custom builds as long as the `_#value` method is
   * directly or indirectly included in the build.
   *
   * In addition to lodash methods, wrappers have `Array` and `String` methods.
   *
   * The wrapper `Array` methods are:
   * `concat`, `join`, `pop`, `push`, `shift`, `sort`, `splice`, and `unshift`
   *
   * The wrapper `String` methods are:
   * `replace` and `split`
   *
   * The wrapper methods that support shortcut fusion are:
   * `at`, `compact`, `drop`, `dropRight`, `dropWhile`, `filter`, `find`,
   * `findLast`, `head`, `initial`, `last`, `map`, `reject`, `reverse`, `slice`,
   * `tail`, `take`, `takeRight`, `takeRightWhile`, `takeWhile`, and `toArray`
   *
   * The chainable wrapper methods are:
   * `after`, `ary`, `assign`, `assignIn`, `assignInWith`, `assignWith`, `at`,
   * `before`, `bind`, `bindAll`, `bindKey`, `castArray`, `chain`, `chunk`,
   * `commit`, `compact`, `concat`, `conforms`, `constant`, `countBy`, `create`,
   * `curry`, `debounce`, `defaults`, `defaultsDeep`, `defer`, `delay`,
   * `difference`, `differenceBy`, `differenceWith`, `drop`, `dropRight`,
   * `dropRightWhile`, `dropWhile`, `extend`, `extendWith`, `fill`, `filter`,
   * `flatMap`, `flatMapDeep`, `flatMapDepth`, `flatten`, `flattenDeep`,
   * `flattenDepth`, `flip`, `flow`, `flowRight`, `fromPairs`, `functions`,
   * `functionsIn`, `groupBy`, `initial`, `intersection`, `intersectionBy`,
   * `intersectionWith`, `invert`, `invertBy`, `invokeMap`, `iteratee`, `keyBy`,
   * `keys`, `keysIn`, `map`, `mapKeys`, `mapValues`, `matches`, `matchesProperty`,
   * `memoize`, `merge`, `mergeWith`, `method`, `methodOf`, `mixin`, `negate`,
   * `nthArg`, `omit`, `omitBy`, `once`, `orderBy`, `over`, `overArgs`,
   * `overEvery`, `overSome`, `partial`, `partialRight`, `partition`, `pick`,
   * `pickBy`, `plant`, `property`, `propertyOf`, `pull`, `pullAll`, `pullAllBy`,
   * `pullAllWith`, `pullAt`, `push`, `range`, `rangeRight`, `rearg`, `reject`,
   * `remove`, `rest`, `reverse`, `sampleSize`, `set`, `setWith`, `shuffle`,
   * `slice`, `sort`, `sortBy`, `splice`, `spread`, `tail`, `take`, `takeRight`,
   * `takeRightWhile`, `takeWhile`, `tap`, `throttle`, `thru`, `toArray`,
   * `toPairs`, `toPairsIn`, `toPath`, `toPlainObject`, `transform`, `unary`,
   * `union`, `unionBy`, `unionWith`, `uniq`, `uniqBy`, `uniqWith`, `unset`,
   * `unshift`, `unzip`, `unzipWith`, `update`, `updateWith`, `values`,
   * `valuesIn`, `without`, `wrap`, `xor`, `xorBy`, `xorWith`, `zip`,
   * `zipObject`, `zipObjectDeep`, and `zipWith`
   *
   * The wrapper methods that are **not** chainable by default are:
   * `add`, `attempt`, `camelCase`, `capitalize`, `ceil`, `clamp`, `clone`,
   * `cloneDeep`, `cloneDeepWith`, `cloneWith`, `conformsTo`, `deburr`,
   * `defaultTo`, `divide`, `each`, `eachRight`, `endsWith`, `eq`, `escape`,
   * `escapeRegExp`, `every`, `find`, `findIndex`, `findKey`, `findLast`,
   * `findLastIndex`, `findLastKey`, `first`, `floor`, `forEach`, `forEachRight`,
   * `forIn`, `forInRight`, `forOwn`, `forOwnRight`, `get`, `gt`, `gte`, `has`,
   * `hasIn`, `head`, `identity`, `includes`, `indexOf`, `inRange`, `invoke`,
   * `isArguments`, `isArray`, `isArrayBuffer`, `isArrayLike`, `isArrayLikeObject`,
   * `isBoolean`, `isBuffer`, `isDate`, `isElement`, `isEmpty`, `isEqual`,
   * `isEqualWith`, `isError`, `isFinite`, `isFunction`, `isInteger`, `isLength`,
   * `isMap`, `isMatch`, `isMatchWith`, `isNaN`, `isNative`, `isNil`, `isNull`,
   * `isNumber`, `isObject`, `isObjectLike`, `isPlainObject`, `isRegExp`,
   * `isSafeInteger`, `isSet`, `isString`, `isUndefined`, `isTypedArray`,
   * `isWeakMap`, `isWeakSet`, `join`, `kebabCase`, `last`, `lastIndexOf`,
   * `lowerCase`, `lowerFirst`, `lt`, `lte`, `max`, `maxBy`, `mean`, `meanBy`,
   * `min`, `minBy`, `multiply`, `noConflict`, `noop`, `now`, `nth`, `pad`,
   * `padEnd`, `padStart`, `parseInt`, `pop`, `random`, `reduce`, `reduceRight`,
   * `repeat`, `result`, `round`, `runInContext`, `sample`, `shift`, `size`,
   * `snakeCase`, `some`, `sortedIndex`, `sortedIndexBy`, `sortedLastIndex`,
   * `sortedLastIndexBy`, `startCase`, `startsWith`, `stubArray`, `stubFalse`,
   * `stubObject`, `stubString`, `stubTrue`, `subtract`, `sum`, `sumBy`,
   * `template`, `times`, `toFinite`, `toInteger`, `toJSON`, `toLength`,
   * `toLower`, `toNumber`, `toSafeInteger`, `toString`, `toUpper`, `trim`,
   * `trimEnd`, `trimStart`, `truncate`, `unescape`, `uniqueId`, `upperCase`,
   * `upperFirst`, `value`, and `words`
   *
   * @name _
   * @constructor
   * @category Seq
   * @param {*} value The value to wrap in a `lodash` instance.
   * @returns {Object} Returns the new `lodash` wrapper instance.
   * @example
   *
   * function square(n) {
   *   return n * n;
   * }
   *
   * var wrapped = _([1, 2, 3]);
   *
   * // Returns an unwrapped value.
   * wrapped.reduce(_.add);
   * // => 6
   *
   * // Returns a wrapped value.
   * var squares = wrapped.map(square);
   *
   * _.isArray(squares);
   * // => false
   *
   * _.isArray(squares.value());
   * // => true
   */function lodash(value){return value instanceof LodashWrapper?value:new LodashWrapper(value);}/**
   * The base implementation of `_.create` without support for assigning
   * properties to the created object.
   *
   * @private
   * @param {Object} proto The object to inherit from.
   * @returns {Object} Returns the new object.
   */var baseCreate=function(){function object(){}return function(proto){if(!isObject(proto)){return{};}if(objectCreate){return objectCreate(proto);}object.prototype=proto;var result=new object();object.prototype=undefined;return result;};}();/**
   * The base constructor for creating `lodash` wrapper objects.
   *
   * @private
   * @param {*} value The value to wrap.
   * @param {boolean} [chainAll] Enable explicit method chain sequences.
   */function LodashWrapper(value,chainAll){this.__wrapped__=value;this.__actions__=[];this.__chain__=!!chainAll;}LodashWrapper.prototype=baseCreate(lodash.prototype);LodashWrapper.prototype.constructor=LodashWrapper;/*------------------------------------------------------------------------*//**
   * Used by `_.defaults` to customize its `_.assignIn` use.
   *
   * @private
   * @param {*} objValue The destination value.
   * @param {*} srcValue The source value.
   * @param {string} key The key of the property to assign.
   * @param {Object} object The parent object of `objValue`.
   * @returns {*} Returns the value to assign.
   */function assignInDefaults(objValue,srcValue,key,object){if(objValue===undefined||eq(objValue,objectProto[key])&&!hasOwnProperty.call(object,key)){return srcValue;}return objValue;}/**
   * Assigns `value` to `key` of `object` if the existing value is not equivalent
   * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
   * for equality comparisons.
   *
   * @private
   * @param {Object} object The object to modify.
   * @param {string} key The key of the property to assign.
   * @param {*} value The value to assign.
   */function assignValue(object,key,value){var objValue=object[key];if(!(hasOwnProperty.call(object,key)&&eq(objValue,value))||value===undefined&&!(key in object)){baseAssignValue(object,key,value);}}/**
   * The base implementation of `assignValue` and `assignMergeValue` without
   * value checks.
   *
   * @private
   * @param {Object} object The object to modify.
   * @param {string} key The key of the property to assign.
   * @param {*} value The value to assign.
   */function baseAssignValue(object,key,value){object[key]=value;}/**
   * The base implementation of `_.delay` and `_.defer` which accepts `args`
   * to provide to `func`.
   *
   * @private
   * @param {Function} func The function to delay.
   * @param {number} wait The number of milliseconds to delay invocation.
   * @param {Array} args The arguments to provide to `func`.
   * @returns {number|Object} Returns the timer id or timeout object.
   */function baseDelay(func,wait,args){if(typeof func!='function'){throw new TypeError(FUNC_ERROR_TEXT);}return setTimeout(function(){func.apply(undefined,args);},wait);}/**
   * The base implementation of `_.forEach` without support for iteratee shorthands.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array|Object} Returns `collection`.
   */var baseEach=createBaseEach(baseForOwn);/**
   * The base implementation of `_.every` without support for iteratee shorthands.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {boolean} Returns `true` if all elements pass the predicate check,
   *  else `false`
   */function baseEvery(collection,predicate){var result=true;baseEach(collection,function(value,index,collection){result=!!predicate(value,index,collection);return result;});return result;}/**
   * The base implementation of methods like `_.max` and `_.min` which accepts a
   * `comparator` to determine the extremum value.
   *
   * @private
   * @param {Array} array The array to iterate over.
   * @param {Function} iteratee The iteratee invoked per iteration.
   * @param {Function} comparator The comparator used to compare values.
   * @returns {*} Returns the extremum value.
   */function baseExtremum(array,iteratee,comparator){var index=-1,length=array.length;while(++index<length){var value=array[index],current=iteratee(value);if(current!=null&&(computed===undefined?current===current&&!false:comparator(current,computed))){var computed=current,result=value;}}return result;}/**
   * The base implementation of `_.filter` without support for iteratee shorthands.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {Array} Returns the new filtered array.
   */function baseFilter(collection,predicate){var result=[];baseEach(collection,function(value,index,collection){if(predicate(value,index,collection)){result.push(value);}});return result;}/**
   * The base implementation of `_.flatten` with support for restricting flattening.
   *
   * @private
   * @param {Array} array The array to flatten.
   * @param {number} depth The maximum recursion depth.
   * @param {boolean} [predicate=isFlattenable] The function invoked per iteration.
   * @param {boolean} [isStrict] Restrict to values that pass `predicate` checks.
   * @param {Array} [result=[]] The initial result value.
   * @returns {Array} Returns the new flattened array.
   */function baseFlatten(array,depth,predicate,isStrict,result){var index=-1,length=array.length;predicate||(predicate=isFlattenable);result||(result=[]);while(++index<length){var value=array[index];if(depth>0&&predicate(value)){if(depth>1){// Recursively flatten arrays (susceptible to call stack limits).
baseFlatten(value,depth-1,predicate,isStrict,result);}else{arrayPush(result,value);}}else if(!isStrict){result[result.length]=value;}}return result;}/**
   * The base implementation of `baseForOwn` which iterates over `object`
   * properties returned by `keysFunc` and invokes `iteratee` for each property.
   * Iteratee functions may exit iteration early by explicitly returning `false`.
   *
   * @private
   * @param {Object} object The object to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @param {Function} keysFunc The function to get the keys of `object`.
   * @returns {Object} Returns `object`.
   */var baseFor=createBaseFor();/**
   * The base implementation of `_.forOwn` without support for iteratee shorthands.
   *
   * @private
   * @param {Object} object The object to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Object} Returns `object`.
   */function baseForOwn(object,iteratee){return object&&baseFor(object,iteratee,keys);}/**
   * The base implementation of `_.functions` which creates an array of
   * `object` function property names filtered from `props`.
   *
   * @private
   * @param {Object} object The object to inspect.
   * @param {Array} props The property names to filter.
   * @returns {Array} Returns the function names.
   */function baseFunctions(object,props){return baseFilter(props,function(key){return isFunction(object[key]);});}/**
   * The base implementation of `getTag` without fallbacks for buggy environments.
   *
   * @private
   * @param {*} value The value to query.
   * @returns {string} Returns the `toStringTag`.
   */function baseGetTag(value){return objectToString(value);}/**
   * The base implementation of `_.gt` which doesn't coerce arguments.
   *
   * @private
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @returns {boolean} Returns `true` if `value` is greater than `other`,
   *  else `false`.
   */function baseGt(value,other){return value>other;}/**
   * The base implementation of `_.isArguments`.
   *
   * @private
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is an `arguments` object,
   */var baseIsArguments=noop;/**
   * The base implementation of `_.isDate` without Node.js optimizations.
   *
   * @private
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a date object, else `false`.
   */function baseIsDate(value){return isObjectLike(value)&&baseGetTag(value)==dateTag;}/**
   * The base implementation of `_.isEqual` which supports partial comparisons
   * and tracks traversed objects.
   *
   * @private
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @param {Function} [customizer] The function to customize comparisons.
   * @param {boolean} [bitmask] The bitmask of comparison flags.
   *  The bitmask may be composed of the following flags:
   *     1 - Unordered comparison
   *     2 - Partial comparison
   * @param {Object} [stack] Tracks traversed `value` and `other` objects.
   * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
   */function baseIsEqual(value,other,customizer,bitmask,stack){if(value===other){return true;}if(value==null||other==null||!isObject(value)&&!isObjectLike(other)){return value!==value&&other!==other;}return baseIsEqualDeep(value,other,baseIsEqual,customizer,bitmask,stack);}/**
   * A specialized version of `baseIsEqual` for arrays and objects which performs
   * deep comparisons and tracks traversed objects enabling objects with circular
   * references to be compared.
   *
   * @private
   * @param {Object} object The object to compare.
   * @param {Object} other The other object to compare.
   * @param {Function} equalFunc The function to determine equivalents of values.
   * @param {Function} [customizer] The function to customize comparisons.
   * @param {number} [bitmask] The bitmask of comparison flags. See `baseIsEqual`
   *  for more details.
   * @param {Object} [stack] Tracks traversed `object` and `other` objects.
   * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
   */function baseIsEqualDeep(object,other,equalFunc,customizer,bitmask,stack){var objIsArr=isArray(object),othIsArr=isArray(other),objTag=arrayTag,othTag=arrayTag;if(!objIsArr){objTag=baseGetTag(object);objTag=objTag==argsTag?objectTag:objTag;}if(!othIsArr){othTag=baseGetTag(other);othTag=othTag==argsTag?objectTag:othTag;}var objIsObj=objTag==objectTag,othIsObj=othTag==objectTag,isSameTag=objTag==othTag;stack||(stack=[]);var objStack=find(stack,function(entry){return entry[0]==object;});var othStack=find(stack,function(entry){return entry[0]==other;});if(objStack&&othStack){return objStack[1]==other;}stack.push([object,other]);stack.push([other,object]);if(isSameTag&&!objIsObj){var result=objIsArr?equalArrays(object,other,equalFunc,customizer,bitmask,stack):equalByTag(object,other,objTag,equalFunc,customizer,bitmask,stack);stack.pop();return result;}if(!(bitmask&PARTIAL_COMPARE_FLAG)){var objIsWrapped=objIsObj&&hasOwnProperty.call(object,'__wrapped__'),othIsWrapped=othIsObj&&hasOwnProperty.call(other,'__wrapped__');if(objIsWrapped||othIsWrapped){var objUnwrapped=objIsWrapped?object.value():object,othUnwrapped=othIsWrapped?other.value():other;var result=equalFunc(objUnwrapped,othUnwrapped,customizer,bitmask,stack);stack.pop();return result;}}if(!isSameTag){return false;}var result=equalObjects(object,other,equalFunc,customizer,bitmask,stack);stack.pop();return result;}/**
   * The base implementation of `_.isRegExp` without Node.js optimizations.
   *
   * @private
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a regexp, else `false`.
   */function baseIsRegExp(value){return isObjectLike(value)&&baseGetTag(value)==regexpTag;}/**
   * The base implementation of `_.iteratee`.
   *
   * @private
   * @param {*} [value=_.identity] The value to convert to an iteratee.
   * @returns {Function} Returns the iteratee.
   */function baseIteratee(func){if(typeof func=='function'){return func;}if(func==null){return identity;}return((typeof func==='undefined'?'undefined':_typeof(func))=='object'?baseMatches:baseProperty)(func);}/**
   * The base implementation of `_.lt` which doesn't coerce arguments.
   *
   * @private
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @returns {boolean} Returns `true` if `value` is less than `other`,
   *  else `false`.
   */function baseLt(value,other){return value<other;}/**
   * The base implementation of `_.map` without support for iteratee shorthands.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array} Returns the new mapped array.
   */function baseMap(collection,iteratee){var index=-1,result=isArrayLike(collection)?Array(collection.length):[];baseEach(collection,function(value,key,collection){result[++index]=iteratee(value,key,collection);});return result;}/**
   * The base implementation of `_.matches` which doesn't clone `source`.
   *
   * @private
   * @param {Object} source The object of property values to match.
   * @returns {Function} Returns the new spec function.
   */function baseMatches(source){var props=nativeKeys(source);return function(object){var length=props.length;if(object==null){return!length;}object=Object(object);while(length--){var key=props[length];if(!(key in object&&baseIsEqual(source[key],object[key],undefined,UNORDERED_COMPARE_FLAG|PARTIAL_COMPARE_FLAG))){return false;}}return true;};}/**
   * The base implementation of `_.pick` without support for individual
   * property identifiers.
   *
   * @private
   * @param {Object} object The source object.
   * @param {string[]} props The property identifiers to pick.
   * @returns {Object} Returns the new object.
   */function basePick(object,props){object=Object(object);return reduce(props,function(result,key){if(key in object){result[key]=object[key];}return result;},{});}/**
   * The base implementation of `_.rest` which doesn't validate or coerce arguments.
   *
   * @private
   * @param {Function} func The function to apply a rest parameter to.
   * @param {number} [start=func.length-1] The start position of the rest parameter.
   * @returns {Function} Returns the new function.
   */function baseRest(func,start){return setToString(overRest(func,start,identity),func+'');}/**
   * The base implementation of `_.slice` without an iteratee call guard.
   *
   * @private
   * @param {Array} array The array to slice.
   * @param {number} [start=0] The start position.
   * @param {number} [end=array.length] The end position.
   * @returns {Array} Returns the slice of `array`.
   */function baseSlice(array,start,end){var index=-1,length=array.length;if(start<0){start=-start>length?0:length+start;}end=end>length?length:end;if(end<0){end+=length;}length=start>end?0:end-start>>>0;start>>>=0;var result=Array(length);while(++index<length){result[index]=array[index+start];}return result;}/**
   * Copies the values of `source` to `array`.
   *
   * @private
   * @param {Array} source The array to copy values from.
   * @param {Array} [array=[]] The array to copy values to.
   * @returns {Array} Returns `array`.
   */function copyArray(source){return baseSlice(source,0,source.length);}/**
   * The base implementation of `_.some` without support for iteratee shorthands.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {boolean} Returns `true` if any element passes the predicate check,
   *  else `false`.
   */function baseSome(collection,predicate){var result;baseEach(collection,function(value,index,collection){result=predicate(value,index,collection);return!result;});return!!result;}/**
   * The base implementation of `wrapperValue` which returns the result of
   * performing a sequence of actions on the unwrapped `value`, where each
   * successive action is supplied the return value of the previous.
   *
   * @private
   * @param {*} value The unwrapped value.
   * @param {Array} actions Actions to perform to resolve the unwrapped value.
   * @returns {*} Returns the resolved value.
   */function baseWrapperValue(value,actions){var result=value;return reduce(actions,function(result,action){return action.func.apply(action.thisArg,arrayPush([result],action.args));},result);}/**
   * Compares values to sort them in ascending order.
   *
   * @private
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @returns {number} Returns the sort order indicator for `value`.
   */function compareAscending(value,other){if(value!==other){var valIsDefined=value!==undefined,valIsNull=value===null,valIsReflexive=value===value,valIsSymbol=false;var othIsDefined=other!==undefined,othIsNull=other===null,othIsReflexive=other===other,othIsSymbol=false;if(!othIsNull&&!othIsSymbol&&!valIsSymbol&&value>other||valIsSymbol&&othIsDefined&&othIsReflexive&&!othIsNull&&!othIsSymbol||valIsNull&&othIsDefined&&othIsReflexive||!valIsDefined&&othIsReflexive||!valIsReflexive){return 1;}if(!valIsNull&&!valIsSymbol&&!othIsSymbol&&value<other||othIsSymbol&&valIsDefined&&valIsReflexive&&!valIsNull&&!valIsSymbol||othIsNull&&valIsDefined&&valIsReflexive||!othIsDefined&&valIsReflexive||!othIsReflexive){return-1;}}return 0;}/**
   * Copies properties of `source` to `object`.
   *
   * @private
   * @param {Object} source The object to copy properties from.
   * @param {Array} props The property identifiers to copy.
   * @param {Object} [object={}] The object to copy properties to.
   * @param {Function} [customizer] The function to customize copied values.
   * @returns {Object} Returns `object`.
   */function copyObject(source,props,object,customizer){var isNew=!object;object||(object={});var index=-1,length=props.length;while(++index<length){var key=props[index];var newValue=customizer?customizer(object[key],source[key],key,object,source):undefined;if(newValue===undefined){newValue=source[key];}if(isNew){baseAssignValue(object,key,newValue);}else{assignValue(object,key,newValue);}}return object;}/**
   * Creates a function like `_.assign`.
   *
   * @private
   * @param {Function} assigner The function to assign values.
   * @returns {Function} Returns the new assigner function.
   */function createAssigner(assigner){return baseRest(function(object,sources){var index=-1,length=sources.length,customizer=length>1?sources[length-1]:undefined;customizer=assigner.length>3&&typeof customizer=='function'?(length--,customizer):undefined;object=Object(object);while(++index<length){var source=sources[index];if(source){assigner(object,source,index,customizer);}}return object;});}/**
   * Creates a `baseEach` or `baseEachRight` function.
   *
   * @private
   * @param {Function} eachFunc The function to iterate over a collection.
   * @param {boolean} [fromRight] Specify iterating from right to left.
   * @returns {Function} Returns the new base function.
   */function createBaseEach(eachFunc,fromRight){return function(collection,iteratee){if(collection==null){return collection;}if(!isArrayLike(collection)){return eachFunc(collection,iteratee);}var length=collection.length,index=fromRight?length:-1,iterable=Object(collection);while(fromRight?index--:++index<length){if(iteratee(iterable[index],index,iterable)===false){break;}}return collection;};}/**
   * Creates a base function for methods like `_.forIn` and `_.forOwn`.
   *
   * @private
   * @param {boolean} [fromRight] Specify iterating from right to left.
   * @returns {Function} Returns the new base function.
   */function createBaseFor(fromRight){return function(object,iteratee,keysFunc){var index=-1,iterable=Object(object),props=keysFunc(object),length=props.length;while(length--){var key=props[fromRight?length:++index];if(iteratee(iterable[key],key,iterable)===false){break;}}return object;};}/**
   * Creates a function that produces an instance of `Ctor` regardless of
   * whether it was invoked as part of a `new` expression or by `call` or `apply`.
   *
   * @private
   * @param {Function} Ctor The constructor to wrap.
   * @returns {Function} Returns the new wrapped function.
   */function createCtor(Ctor){return function(){// Use a `switch` statement to work with class constructors. See
// http://ecma-international.org/ecma-262/7.0/#sec-ecmascript-function-objects-call-thisargument-argumentslist
// for more details.
var args=arguments;var thisBinding=baseCreate(Ctor.prototype),result=Ctor.apply(thisBinding,args);// Mimic the constructor's `return` behavior.
// See https://es5.github.io/#x13.2.2 for more details.
return isObject(result)?result:thisBinding;};}/**
   * Creates a `_.find` or `_.findLast` function.
   *
   * @private
   * @param {Function} findIndexFunc The function to find the collection index.
   * @returns {Function} Returns the new find function.
   */function createFind(findIndexFunc){return function(collection,predicate,fromIndex){var iterable=Object(collection);if(!isArrayLike(collection)){var iteratee=baseIteratee(predicate,3);collection=keys(collection);predicate=function predicate(key){return iteratee(iterable[key],key,iterable);};}var index=findIndexFunc(collection,predicate,fromIndex);return index>-1?iterable[iteratee?collection[index]:index]:undefined;};}/**
   * Creates a function that wraps `func` to invoke it with the `this` binding
   * of `thisArg` and `partials` prepended to the arguments it receives.
   *
   * @private
   * @param {Function} func The function to wrap.
   * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
   * @param {*} thisArg The `this` binding of `func`.
   * @param {Array} partials The arguments to prepend to those provided to
   *  the new function.
   * @returns {Function} Returns the new wrapped function.
   */function createPartial(func,bitmask,thisArg,partials){if(typeof func!='function'){throw new TypeError(FUNC_ERROR_TEXT);}var isBind=bitmask&BIND_FLAG,Ctor=createCtor(func);function wrapper(){var argsIndex=-1,argsLength=arguments.length,leftIndex=-1,leftLength=partials.length,args=Array(leftLength+argsLength),fn=this&&this!==root&&this instanceof wrapper?Ctor:func;while(++leftIndex<leftLength){args[leftIndex]=partials[leftIndex];}while(argsLength--){args[leftIndex++]=arguments[++argsIndex];}return fn.apply(isBind?thisArg:this,args);}return wrapper;}/**
   * A specialized version of `baseIsEqualDeep` for arrays with support for
   * partial deep comparisons.
   *
   * @private
   * @param {Array} array The array to compare.
   * @param {Array} other The other array to compare.
   * @param {Function} equalFunc The function to determine equivalents of values.
   * @param {Function} customizer The function to customize comparisons.
   * @param {number} bitmask The bitmask of comparison flags. See `baseIsEqual`
   *  for more details.
   * @param {Object} stack Tracks traversed `array` and `other` objects.
   * @returns {boolean} Returns `true` if the arrays are equivalent, else `false`.
   */function equalArrays(array,other,equalFunc,customizer,bitmask,stack){var isPartial=bitmask&PARTIAL_COMPARE_FLAG,arrLength=array.length,othLength=other.length;if(arrLength!=othLength&&!(isPartial&&othLength>arrLength)){return false;}var index=-1,result=true,seen=bitmask&UNORDERED_COMPARE_FLAG?[]:undefined;// Ignore non-index properties.
while(++index<arrLength){var arrValue=array[index],othValue=other[index];var compared;if(compared!==undefined){if(compared){continue;}result=false;break;}// Recursively compare arrays (susceptible to call stack limits).
if(seen){if(!baseSome(other,function(othValue,othIndex){if(!indexOf(seen,othIndex)&&(arrValue===othValue||equalFunc(arrValue,othValue,customizer,bitmask,stack))){return seen.push(othIndex);}})){result=false;break;}}else if(!(arrValue===othValue||equalFunc(arrValue,othValue,customizer,bitmask,stack))){result=false;break;}}return result;}/**
   * A specialized version of `baseIsEqualDeep` for comparing objects of
   * the same `toStringTag`.
   *
   * **Note:** This function only supports comparing values with tags of
   * `Boolean`, `Date`, `Error`, `Number`, `RegExp`, or `String`.
   *
   * @private
   * @param {Object} object The object to compare.
   * @param {Object} other The other object to compare.
   * @param {string} tag The `toStringTag` of the objects to compare.
   * @param {Function} equalFunc The function to determine equivalents of values.
   * @param {Function} customizer The function to customize comparisons.
   * @param {number} bitmask The bitmask of comparison flags. See `baseIsEqual`
   *  for more details.
   * @param {Object} stack Tracks traversed `object` and `other` objects.
   * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
   */function equalByTag(object,other,tag,equalFunc,customizer,bitmask,stack){switch(tag){case boolTag:case dateTag:case numberTag:// Coerce booleans to `1` or `0` and dates to milliseconds.
// Invalid dates are coerced to `NaN`.
return eq(+object,+other);case errorTag:return object.name==other.name&&object.message==other.message;case regexpTag:case stringTag:// Coerce regexes to strings and treat strings, primitives and objects,
// as equal. See http://www.ecma-international.org/ecma-262/7.0/#sec-regexp.prototype.tostring
// for more details.
return object==other+'';}return false;}/**
   * A specialized version of `baseIsEqualDeep` for objects with support for
   * partial deep comparisons.
   *
   * @private
   * @param {Object} object The object to compare.
   * @param {Object} other The other object to compare.
   * @param {Function} equalFunc The function to determine equivalents of values.
   * @param {Function} customizer The function to customize comparisons.
   * @param {number} bitmask The bitmask of comparison flags. See `baseIsEqual`
   *  for more details.
   * @param {Object} stack Tracks traversed `object` and `other` objects.
   * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
   */function equalObjects(object,other,equalFunc,customizer,bitmask,stack){var isPartial=bitmask&PARTIAL_COMPARE_FLAG,objProps=keys(object),objLength=objProps.length,othProps=keys(other),othLength=othProps.length;if(objLength!=othLength&&!isPartial){return false;}var index=objLength;while(index--){var key=objProps[index];if(!(isPartial?key in other:hasOwnProperty.call(other,key))){return false;}}var result=true;var skipCtor=isPartial;while(++index<objLength){key=objProps[index];var objValue=object[key],othValue=other[key];var compared;// Recursively compare objects (susceptible to call stack limits).
if(!(compared===undefined?objValue===othValue||equalFunc(objValue,othValue,customizer,bitmask,stack):compared)){result=false;break;}skipCtor||(skipCtor=key=='constructor');}if(result&&!skipCtor){var objCtor=object.constructor,othCtor=other.constructor;// Non `Object` object instances with different constructors are not equal.
if(objCtor!=othCtor&&'constructor'in object&&'constructor'in other&&!(typeof objCtor=='function'&&objCtor instanceof objCtor&&typeof othCtor=='function'&&othCtor instanceof othCtor)){result=false;}}return result;}/**
   * A specialized version of `baseRest` which flattens the rest array.
   *
   * @private
   * @param {Function} func The function to apply a rest parameter to.
   * @returns {Function} Returns the new function.
   */function flatRest(func){return setToString(overRest(func,undefined,flatten),func+'');}/**
   * Checks if `value` is a flattenable `arguments` object or array.
   *
   * @private
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is flattenable, else `false`.
   */function isFlattenable(value){return isArray(value)||isArguments(value);}/**
   * This function is like
   * [`Object.keys`](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
   * except that it includes inherited enumerable properties.
   *
   * @private
   * @param {Object} object The object to query.
   * @returns {Array} Returns the array of property names.
   */function nativeKeysIn(object){var result=[];if(object!=null){for(var key in Object(object)){result.push(key);}}return result;}/**
   * Converts `value` to a string using `Object.prototype.toString`.
   *
   * @private
   * @param {*} value The value to convert.
   * @returns {string} Returns the converted string.
   */function objectToString(value){return nativeObjectToString.call(value);}/**
   * A specialized version of `baseRest` which transforms the rest array.
   *
   * @private
   * @param {Function} func The function to apply a rest parameter to.
   * @param {number} [start=func.length-1] The start position of the rest parameter.
   * @param {Function} transform The rest array transform.
   * @returns {Function} Returns the new function.
   */function overRest(func,start,transform){start=nativeMax(start===undefined?func.length-1:start,0);return function(){var args=arguments,index=-1,length=nativeMax(args.length-start,0),array=Array(length);while(++index<length){array[index]=args[start+index];}index=-1;var otherArgs=Array(start+1);while(++index<start){otherArgs[index]=args[index];}otherArgs[start]=transform(array);return func.apply(this,otherArgs);};}/**
   * Sets the `toString` method of `func` to return `string`.
   *
   * @private
   * @param {Function} func The function to modify.
   * @param {Function} string The `toString` result.
   * @returns {Function} Returns `func`.
   */var setToString=identity;/**
   * Converts `value` to a string key if it's not a string or symbol.
   *
   * @private
   * @param {*} value The value to inspect.
   * @returns {string|symbol} Returns the key.
   */var toKey=String;/*------------------------------------------------------------------------*//**
   * Creates an array with all falsey values removed. The values `false`, `null`,
   * `0`, `""`, `undefined`, and `NaN` are falsey.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Array
   * @param {Array} array The array to compact.
   * @returns {Array} Returns the new array of filtered values.
   * @example
   *
   * _.compact([0, 1, false, 2, '', 3]);
   * // => [1, 2, 3]
   */function compact(array){return baseFilter(array,Boolean);}/**
   * Creates a new array concatenating `array` with any additional arrays
   * and/or values.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Array
   * @param {Array} array The array to concatenate.
   * @param {...*} [values] The values to concatenate.
   * @returns {Array} Returns the new concatenated array.
   * @example
   *
   * var array = [1];
   * var other = _.concat(array, 2, [3], [[4]]);
   *
   * console.log(other);
   * // => [1, 2, 3, [4]]
   *
   * console.log(array);
   * // => [1]
   */function concat(){var length=arguments.length;if(!length){return[];}var args=Array(length-1),array=arguments[0],index=length;while(index--){args[index-1]=arguments[index];}return arrayPush(isArray(array)?copyArray(array):[array],baseFlatten(args,1));}/**
   * This method is like `_.find` except that it returns the index of the first
   * element `predicate` returns truthy for instead of the element itself.
   *
   * @static
   * @memberOf _
   * @since 1.1.0
   * @category Array
   * @param {Array} array The array to inspect.
   * @param {Function} [predicate=_.identity] The function invoked per iteration.
   * @param {number} [fromIndex=0] The index to search from.
   * @returns {number} Returns the index of the found element, else `-1`.
   * @example
   *
   * var users = [
   *   { 'user': 'barney',  'active': false },
   *   { 'user': 'fred',    'active': false },
   *   { 'user': 'pebbles', 'active': true }
   * ];
   *
   * _.findIndex(users, function(o) { return o.user == 'barney'; });
   * // => 0
   *
   * // The `_.matches` iteratee shorthand.
   * _.findIndex(users, { 'user': 'fred', 'active': false });
   * // => 1
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.findIndex(users, ['active', false]);
   * // => 0
   *
   * // The `_.property` iteratee shorthand.
   * _.findIndex(users, 'active');
   * // => 2
   */function findIndex(array,predicate,fromIndex){var length=array==null?0:array.length;if(!length){return-1;}var index=fromIndex==null?0:toInteger(fromIndex);if(index<0){index=nativeMax(length+index,0);}return baseFindIndex(array,baseIteratee(predicate,3),index);}/**
   * Flattens `array` a single level deep.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Array
   * @param {Array} array The array to flatten.
   * @returns {Array} Returns the new flattened array.
   * @example
   *
   * _.flatten([1, [2, [3, [4]], 5]]);
   * // => [1, 2, [3, [4]], 5]
   */function flatten(array){var length=array==null?0:array.length;return length?baseFlatten(array,1):[];}/**
   * Recursively flattens `array`.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Array
   * @param {Array} array The array to flatten.
   * @returns {Array} Returns the new flattened array.
   * @example
   *
   * _.flattenDeep([1, [2, [3, [4]], 5]]);
   * // => [1, 2, 3, 4, 5]
   */function flattenDeep(array){var length=array==null?0:array.length;return length?baseFlatten(array,INFINITY):[];}/**
   * Gets the first element of `array`.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @alias first
   * @category Array
   * @param {Array} array The array to query.
   * @returns {*} Returns the first element of `array`.
   * @example
   *
   * _.head([1, 2, 3]);
   * // => 1
   *
   * _.head([]);
   * // => undefined
   */function head(array){return array&&array.length?array[0]:undefined;}/**
   * Gets the index at which the first occurrence of `value` is found in `array`
   * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
   * for equality comparisons. If `fromIndex` is negative, it's used as the
   * offset from the end of `array`.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Array
   * @param {Array} array The array to inspect.
   * @param {*} value The value to search for.
   * @param {number} [fromIndex=0] The index to search from.
   * @returns {number} Returns the index of the matched value, else `-1`.
   * @example
   *
   * _.indexOf([1, 2, 1, 2], 2);
   * // => 1
   *
   * // Search from the `fromIndex`.
   * _.indexOf([1, 2, 1, 2], 2, 2);
   * // => 3
   */function indexOf(array,value,fromIndex){var length=array==null?0:array.length;if(typeof fromIndex=='number'){fromIndex=fromIndex<0?nativeMax(length+fromIndex,0):fromIndex;}else{fromIndex=0;}var index=(fromIndex||0)-1,isReflexive=value===value;while(++index<length){var other=array[index];if(isReflexive?other===value:other!==other){return index;}}return-1;}/**
   * Gets the last element of `array`.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Array
   * @param {Array} array The array to query.
   * @returns {*} Returns the last element of `array`.
   * @example
   *
   * _.last([1, 2, 3]);
   * // => 3
   */function last(array){var length=array==null?0:array.length;return length?array[length-1]:undefined;}/**
   * Creates a slice of `array` from `start` up to, but not including, `end`.
   *
   * **Note:** This method is used instead of
   * [`Array#slice`](https://mdn.io/Array/slice) to ensure dense arrays are
   * returned.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Array
   * @param {Array} array The array to slice.
   * @param {number} [start=0] The start position.
   * @param {number} [end=array.length] The end position.
   * @returns {Array} Returns the slice of `array`.
   */function slice(array,start,end){var length=array==null?0:array.length;start=start==null?0:+start;end=end===undefined?length:+end;return length?baseSlice(array,start,end):[];}/*------------------------------------------------------------------------*//**
   * Creates a `lodash` wrapper instance that wraps `value` with explicit method
   * chain sequences enabled. The result of such sequences must be unwrapped
   * with `_#value`.
   *
   * @static
   * @memberOf _
   * @since 1.3.0
   * @category Seq
   * @param {*} value The value to wrap.
   * @returns {Object} Returns the new `lodash` wrapper instance.
   * @example
   *
   * var users = [
   *   { 'user': 'barney',  'age': 36 },
   *   { 'user': 'fred',    'age': 40 },
   *   { 'user': 'pebbles', 'age': 1 }
   * ];
   *
   * var youngest = _
   *   .chain(users)
   *   .sortBy('age')
   *   .map(function(o) {
   *     return o.user + ' is ' + o.age;
   *   })
   *   .head()
   *   .value();
   * // => 'pebbles is 1'
   */function chain(value){var result=lodash(value);result.__chain__=true;return result;}/**
   * This method invokes `interceptor` and returns `value`. The interceptor
   * is invoked with one argument; (value). The purpose of this method is to
   * "tap into" a method chain sequence in order to modify intermediate results.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Seq
   * @param {*} value The value to provide to `interceptor`.
   * @param {Function} interceptor The function to invoke.
   * @returns {*} Returns `value`.
   * @example
   *
   * _([1, 2, 3])
   *  .tap(function(array) {
   *    // Mutate input array.
   *    array.pop();
   *  })
   *  .reverse()
   *  .value();
   * // => [2, 1]
   */function tap(value,interceptor){interceptor(value);return value;}/**
   * This method is like `_.tap` except that it returns the result of `interceptor`.
   * The purpose of this method is to "pass thru" values replacing intermediate
   * results in a method chain sequence.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Seq
   * @param {*} value The value to provide to `interceptor`.
   * @param {Function} interceptor The function to invoke.
   * @returns {*} Returns the result of `interceptor`.
   * @example
   *
   * _('  abc  ')
   *  .chain()
   *  .trim()
   *  .thru(function(value) {
   *    return [value];
   *  })
   *  .value();
   * // => ['abc']
   */function thru(value,interceptor){return interceptor(value);}/**
   * Creates a `lodash` wrapper instance with explicit method chain sequences enabled.
   *
   * @name chain
   * @memberOf _
   * @since 0.1.0
   * @category Seq
   * @returns {Object} Returns the new `lodash` wrapper instance.
   * @example
   *
   * var users = [
   *   { 'user': 'barney', 'age': 36 },
   *   { 'user': 'fred',   'age': 40 }
   * ];
   *
   * // A sequence without explicit chaining.
   * _(users).head();
   * // => { 'user': 'barney', 'age': 36 }
   *
   * // A sequence with explicit chaining.
   * _(users)
   *   .chain()
   *   .head()
   *   .pick('user')
   *   .value();
   * // => { 'user': 'barney' }
   */function wrapperChain(){return chain(this);}/**
   * Executes the chain sequence to resolve the unwrapped value.
   *
   * @name value
   * @memberOf _
   * @since 0.1.0
   * @alias toJSON, valueOf
   * @category Seq
   * @returns {*} Returns the resolved unwrapped value.
   * @example
   *
   * _([1, 2, 3]).value();
   * // => [1, 2, 3]
   */function wrapperValue(){return baseWrapperValue(this.__wrapped__,this.__actions__);}/*------------------------------------------------------------------------*//**
   * Checks if `predicate` returns truthy for **all** elements of `collection`.
   * Iteration is stopped once `predicate` returns falsey. The predicate is
   * invoked with three arguments: (value, index|key, collection).
   *
   * **Note:** This method returns `true` for
   * [empty collections](https://en.wikipedia.org/wiki/Empty_set) because
   * [everything is true](https://en.wikipedia.org/wiki/Vacuous_truth) of
   * elements of empty collections.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [predicate=_.identity] The function invoked per iteration.
   * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
   * @returns {boolean} Returns `true` if all elements pass the predicate check,
   *  else `false`.
   * @example
   *
   * _.every([true, 1, null, 'yes'], Boolean);
   * // => false
   *
   * var users = [
   *   { 'user': 'barney', 'age': 36, 'active': false },
   *   { 'user': 'fred',   'age': 40, 'active': false }
   * ];
   *
   * // The `_.matches` iteratee shorthand.
   * _.every(users, { 'user': 'barney', 'active': false });
   * // => false
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.every(users, ['active', false]);
   * // => true
   *
   * // The `_.property` iteratee shorthand.
   * _.every(users, 'active');
   * // => false
   */function every(collection,predicate,guard){predicate=guard?undefined:predicate;return baseEvery(collection,baseIteratee(predicate));}/**
   * Iterates over elements of `collection`, returning an array of all elements
   * `predicate` returns truthy for. The predicate is invoked with three
   * arguments: (value, index|key, collection).
   *
   * **Note:** Unlike `_.remove`, this method returns a new array.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [predicate=_.identity] The function invoked per iteration.
   * @returns {Array} Returns the new filtered array.
   * @see _.reject
   * @example
   *
   * var users = [
   *   { 'user': 'barney', 'age': 36, 'active': true },
   *   { 'user': 'fred',   'age': 40, 'active': false }
   * ];
   *
   * _.filter(users, function(o) { return !o.active; });
   * // => objects for ['fred']
   *
   * // The `_.matches` iteratee shorthand.
   * _.filter(users, { 'age': 36, 'active': true });
   * // => objects for ['barney']
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.filter(users, ['active', false]);
   * // => objects for ['fred']
   *
   * // The `_.property` iteratee shorthand.
   * _.filter(users, 'active');
   * // => objects for ['barney']
   */function filter(collection,predicate){return baseFilter(collection,baseIteratee(predicate));}/**
   * Iterates over elements of `collection`, returning the first element
   * `predicate` returns truthy for. The predicate is invoked with three
   * arguments: (value, index|key, collection).
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to inspect.
   * @param {Function} [predicate=_.identity] The function invoked per iteration.
   * @param {number} [fromIndex=0] The index to search from.
   * @returns {*} Returns the matched element, else `undefined`.
   * @example
   *
   * var users = [
   *   { 'user': 'barney',  'age': 36, 'active': true },
   *   { 'user': 'fred',    'age': 40, 'active': false },
   *   { 'user': 'pebbles', 'age': 1,  'active': true }
   * ];
   *
   * _.find(users, function(o) { return o.age < 40; });
   * // => object for 'barney'
   *
   * // The `_.matches` iteratee shorthand.
   * _.find(users, { 'age': 1, 'active': true });
   * // => object for 'pebbles'
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.find(users, ['active', false]);
   * // => object for 'fred'
   *
   * // The `_.property` iteratee shorthand.
   * _.find(users, 'active');
   * // => object for 'barney'
   */var find=createFind(findIndex);/**
   * Iterates over elements of `collection` and invokes `iteratee` for each element.
   * The iteratee is invoked with three arguments: (value, index|key, collection).
   * Iteratee functions may exit iteration early by explicitly returning `false`.
   *
   * **Note:** As with other "Collections" methods, objects with a "length"
   * property are iterated like arrays. To avoid this behavior use `_.forIn`
   * or `_.forOwn` for object iteration.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @alias each
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [iteratee=_.identity] The function invoked per iteration.
   * @returns {Array|Object} Returns `collection`.
   * @see _.forEachRight
   * @example
   *
   * _.forEach([1, 2], function(value) {
   *   console.log(value);
   * });
   * // => Logs `1` then `2`.
   *
   * _.forEach({ 'a': 1, 'b': 2 }, function(value, key) {
   *   console.log(key);
   * });
   * // => Logs 'a' then 'b' (iteration order is not guaranteed).
   */function forEach(collection,iteratee){return baseEach(collection,baseIteratee(iteratee));}/**
   * Creates an array of values by running each element in `collection` thru
   * `iteratee`. The iteratee is invoked with three arguments:
   * (value, index|key, collection).
   *
   * Many lodash methods are guarded to work as iteratees for methods like
   * `_.every`, `_.filter`, `_.map`, `_.mapValues`, `_.reject`, and `_.some`.
   *
   * The guarded methods are:
   * `ary`, `chunk`, `curry`, `curryRight`, `drop`, `dropRight`, `every`,
   * `fill`, `invert`, `parseInt`, `random`, `range`, `rangeRight`, `repeat`,
   * `sampleSize`, `slice`, `some`, `sortBy`, `split`, `take`, `takeRight`,
   * `template`, `trim`, `trimEnd`, `trimStart`, and `words`
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [iteratee=_.identity] The function invoked per iteration.
   * @returns {Array} Returns the new mapped array.
   * @example
   *
   * function square(n) {
   *   return n * n;
   * }
   *
   * _.map([4, 8], square);
   * // => [16, 64]
   *
   * _.map({ 'a': 4, 'b': 8 }, square);
   * // => [16, 64] (iteration order is not guaranteed)
   *
   * var users = [
   *   { 'user': 'barney' },
   *   { 'user': 'fred' }
   * ];
   *
   * // The `_.property` iteratee shorthand.
   * _.map(users, 'user');
   * // => ['barney', 'fred']
   */function map(collection,iteratee){return baseMap(collection,baseIteratee(iteratee));}/**
   * Reduces `collection` to a value which is the accumulated result of running
   * each element in `collection` thru `iteratee`, where each successive
   * invocation is supplied the return value of the previous. If `accumulator`
   * is not given, the first element of `collection` is used as the initial
   * value. The iteratee is invoked with four arguments:
   * (accumulator, value, index|key, collection).
   *
   * Many lodash methods are guarded to work as iteratees for methods like
   * `_.reduce`, `_.reduceRight`, and `_.transform`.
   *
   * The guarded methods are:
   * `assign`, `defaults`, `defaultsDeep`, `includes`, `merge`, `orderBy`,
   * and `sortBy`
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [iteratee=_.identity] The function invoked per iteration.
   * @param {*} [accumulator] The initial value.
   * @returns {*} Returns the accumulated value.
   * @see _.reduceRight
   * @example
   *
   * _.reduce([1, 2], function(sum, n) {
   *   return sum + n;
   * }, 0);
   * // => 3
   *
   * _.reduce({ 'a': 1, 'b': 2, 'c': 1 }, function(result, value, key) {
   *   (result[value] || (result[value] = [])).push(key);
   *   return result;
   * }, {});
   * // => { '1': ['a', 'c'], '2': ['b'] } (iteration order is not guaranteed)
   */function reduce(collection,iteratee,accumulator){return baseReduce(collection,baseIteratee(iteratee),accumulator,arguments.length<3,baseEach);}/**
   * Gets the size of `collection` by returning its length for array-like
   * values or the number of own enumerable string keyed properties for objects.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object|string} collection The collection to inspect.
   * @returns {number} Returns the collection size.
   * @example
   *
   * _.size([1, 2, 3]);
   * // => 3
   *
   * _.size({ 'a': 1, 'b': 2 });
   * // => 2
   *
   * _.size('pebbles');
   * // => 7
   */function size(collection){if(collection==null){return 0;}collection=isArrayLike(collection)?collection:nativeKeys(collection);return collection.length;}/**
   * Checks if `predicate` returns truthy for **any** element of `collection`.
   * Iteration is stopped once `predicate` returns truthy. The predicate is
   * invoked with three arguments: (value, index|key, collection).
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} [predicate=_.identity] The function invoked per iteration.
   * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
   * @returns {boolean} Returns `true` if any element passes the predicate check,
   *  else `false`.
   * @example
   *
   * _.some([null, 0, 'yes', false], Boolean);
   * // => true
   *
   * var users = [
   *   { 'user': 'barney', 'active': true },
   *   { 'user': 'fred',   'active': false }
   * ];
   *
   * // The `_.matches` iteratee shorthand.
   * _.some(users, { 'user': 'barney', 'active': false });
   * // => false
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.some(users, ['active', false]);
   * // => true
   *
   * // The `_.property` iteratee shorthand.
   * _.some(users, 'active');
   * // => true
   */function some(collection,predicate,guard){predicate=guard?undefined:predicate;return baseSome(collection,baseIteratee(predicate));}/**
   * Creates an array of elements, sorted in ascending order by the results of
   * running each element in a collection thru each iteratee. This method
   * performs a stable sort, that is, it preserves the original sort order of
   * equal elements. The iteratees are invoked with one argument: (value).
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Collection
   * @param {Array|Object} collection The collection to iterate over.
   * @param {...(Function|Function[])} [iteratees=[_.identity]]
   *  The iteratees to sort by.
   * @returns {Array} Returns the new sorted array.
   * @example
   *
   * var users = [
   *   { 'user': 'fred',   'age': 48 },
   *   { 'user': 'barney', 'age': 36 },
   *   { 'user': 'fred',   'age': 40 },
   *   { 'user': 'barney', 'age': 34 }
   * ];
   *
   * _.sortBy(users, [function(o) { return o.user; }]);
   * // => objects for [['barney', 36], ['barney', 34], ['fred', 48], ['fred', 40]]
   *
   * _.sortBy(users, ['user', 'age']);
   * // => objects for [['barney', 34], ['barney', 36], ['fred', 40], ['fred', 48]]
   */function sortBy(collection,iteratee){var index=0;iteratee=baseIteratee(iteratee);return baseMap(baseMap(collection,function(value,key,collection){return{'value':value,'index':index++,'criteria':iteratee(value,key,collection)};}).sort(function(object,other){return compareAscending(object.criteria,other.criteria)||object.index-other.index;}),baseProperty('value'));}/*------------------------------------------------------------------------*//**
   * Creates a function that invokes `func`, with the `this` binding and arguments
   * of the created function, while it's called less than `n` times. Subsequent
   * calls to the created function return the result of the last `func` invocation.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Function
   * @param {number} n The number of calls at which `func` is no longer invoked.
   * @param {Function} func The function to restrict.
   * @returns {Function} Returns the new restricted function.
   * @example
   *
   * jQuery(element).on('click', _.before(5, addContactToList));
   * // => Allows adding up to 4 contacts to the list.
   */function before(n,func){var result;if(typeof func!='function'){throw new TypeError(FUNC_ERROR_TEXT);}n=toInteger(n);return function(){if(--n>0){result=func.apply(this,arguments);}if(n<=1){func=undefined;}return result;};}/**
   * Creates a function that invokes `func` with the `this` binding of `thisArg`
   * and `partials` prepended to the arguments it receives.
   *
   * The `_.bind.placeholder` value, which defaults to `_` in monolithic builds,
   * may be used as a placeholder for partially applied arguments.
   *
   * **Note:** Unlike native `Function#bind`, this method doesn't set the "length"
   * property of bound functions.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Function
   * @param {Function} func The function to bind.
   * @param {*} thisArg The `this` binding of `func`.
   * @param {...*} [partials] The arguments to be partially applied.
   * @returns {Function} Returns the new bound function.
   * @example
   *
   * function greet(greeting, punctuation) {
   *   return greeting + ' ' + this.user + punctuation;
   * }
   *
   * var object = { 'user': 'fred' };
   *
   * var bound = _.bind(greet, object, 'hi');
   * bound('!');
   * // => 'hi fred!'
   *
   * // Bound with placeholders.
   * var bound = _.bind(greet, object, _, '!');
   * bound('hi');
   * // => 'hi fred!'
   */var bind=baseRest(function(func,thisArg,partials){return createPartial(func,BIND_FLAG|PARTIAL_FLAG,thisArg,partials);});/**
   * Defers invoking the `func` until the current call stack has cleared. Any
   * additional arguments are provided to `func` when it's invoked.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Function
   * @param {Function} func The function to defer.
   * @param {...*} [args] The arguments to invoke `func` with.
   * @returns {number} Returns the timer id.
   * @example
   *
   * _.defer(function(text) {
   *   console.log(text);
   * }, 'deferred');
   * // => Logs 'deferred' after one millisecond.
   */var defer=baseRest(function(func,args){return baseDelay(func,1,args);});/**
   * Invokes `func` after `wait` milliseconds. Any additional arguments are
   * provided to `func` when it's invoked.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Function
   * @param {Function} func The function to delay.
   * @param {number} wait The number of milliseconds to delay invocation.
   * @param {...*} [args] The arguments to invoke `func` with.
   * @returns {number} Returns the timer id.
   * @example
   *
   * _.delay(function(text) {
   *   console.log(text);
   * }, 1000, 'later');
   * // => Logs 'later' after one second.
   */var delay=baseRest(function(func,wait,args){return baseDelay(func,toNumber(wait)||0,args);});/**
   * Creates a function that negates the result of the predicate `func`. The
   * `func` predicate is invoked with the `this` binding and arguments of the
   * created function.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Function
   * @param {Function} predicate The predicate to negate.
   * @returns {Function} Returns the new negated function.
   * @example
   *
   * function isEven(n) {
   *   return n % 2 == 0;
   * }
   *
   * _.filter([1, 2, 3, 4, 5, 6], _.negate(isEven));
   * // => [1, 3, 5]
   */function negate(predicate){if(typeof predicate!='function'){throw new TypeError(FUNC_ERROR_TEXT);}return function(){var args=arguments;return!predicate.apply(this,args);};}/**
   * Creates a function that is restricted to invoking `func` once. Repeat calls
   * to the function return the value of the first invocation. The `func` is
   * invoked with the `this` binding and arguments of the created function.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Function
   * @param {Function} func The function to restrict.
   * @returns {Function} Returns the new restricted function.
   * @example
   *
   * var initialize = _.once(createApplication);
   * initialize();
   * initialize();
   * // => `createApplication` is invoked once
   */function once(func){return before(2,func);}/*------------------------------------------------------------------------*//**
   * Creates a shallow clone of `value`.
   *
   * **Note:** This method is loosely based on the
   * [structured clone algorithm](https://mdn.io/Structured_clone_algorithm)
   * and supports cloning arrays, array buffers, booleans, date objects, maps,
   * numbers, `Object` objects, regexes, sets, strings, symbols, and typed
   * arrays. The own enumerable properties of `arguments` objects are cloned
   * as plain objects. An empty object is returned for uncloneable values such
   * as error objects, functions, DOM nodes, and WeakMaps.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to clone.
   * @returns {*} Returns the cloned value.
   * @see _.cloneDeep
   * @example
   *
   * var objects = [{ 'a': 1 }, { 'b': 2 }];
   *
   * var shallow = _.clone(objects);
   * console.log(shallow[0] === objects[0]);
   * // => true
   */function clone(value){if(!isObject(value)){return value;}return isArray(value)?copyArray(value):copyObject(value,nativeKeys(value));}/**
   * Performs a
   * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
   * comparison between two values to determine if they are equivalent.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
   * @example
   *
   * var object = { 'a': 1 };
   * var other = { 'a': 1 };
   *
   * _.eq(object, object);
   * // => true
   *
   * _.eq(object, other);
   * // => false
   *
   * _.eq('a', 'a');
   * // => true
   *
   * _.eq('a', Object('a'));
   * // => false
   *
   * _.eq(NaN, NaN);
   * // => true
   */function eq(value,other){return value===other||value!==value&&other!==other;}/**
   * Checks if `value` is likely an `arguments` object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is an `arguments` object,
   *  else `false`.
   * @example
   *
   * _.isArguments(function() { return arguments; }());
   * // => true
   *
   * _.isArguments([1, 2, 3]);
   * // => false
   */var isArguments=baseIsArguments(function(){return arguments;}())?baseIsArguments:function(value){return isObjectLike(value)&&hasOwnProperty.call(value,'callee')&&!propertyIsEnumerable.call(value,'callee');};/**
   * Checks if `value` is classified as an `Array` object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is an array, else `false`.
   * @example
   *
   * _.isArray([1, 2, 3]);
   * // => true
   *
   * _.isArray(document.body.children);
   * // => false
   *
   * _.isArray('abc');
   * // => false
   *
   * _.isArray(_.noop);
   * // => false
   */var isArray=Array.isArray;/**
   * Checks if `value` is array-like. A value is considered array-like if it's
   * not a function and has a `value.length` that's an integer greater than or
   * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
   * @example
   *
   * _.isArrayLike([1, 2, 3]);
   * // => true
   *
   * _.isArrayLike(document.body.children);
   * // => true
   *
   * _.isArrayLike('abc');
   * // => true
   *
   * _.isArrayLike(_.noop);
   * // => false
   */function isArrayLike(value){return value!=null&&isLength(value.length)&&!isFunction(value);}/**
   * Checks if `value` is classified as a boolean primitive or object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a boolean, else `false`.
   * @example
   *
   * _.isBoolean(false);
   * // => true
   *
   * _.isBoolean(null);
   * // => false
   */function isBoolean(value){return value===true||value===false||isObjectLike(value)&&baseGetTag(value)==boolTag;}/**
   * Checks if `value` is classified as a `Date` object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a date object, else `false`.
   * @example
   *
   * _.isDate(new Date);
   * // => true
   *
   * _.isDate('Mon April 23 2012');
   * // => false
   */var isDate=baseIsDate;/**
   * Checks if `value` is an empty object, collection, map, or set.
   *
   * Objects are considered empty if they have no own enumerable string keyed
   * properties.
   *
   * Array-like values such as `arguments` objects, arrays, buffers, strings, or
   * jQuery-like collections are considered empty if they have a `length` of `0`.
   * Similarly, maps and sets are considered empty if they have a `size` of `0`.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is empty, else `false`.
   * @example
   *
   * _.isEmpty(null);
   * // => true
   *
   * _.isEmpty(true);
   * // => true
   *
   * _.isEmpty(1);
   * // => true
   *
   * _.isEmpty([1, 2, 3]);
   * // => false
   *
   * _.isEmpty({ 'a': 1 });
   * // => false
   */function isEmpty(value){if(isArrayLike(value)&&(isArray(value)||isString(value)||isFunction(value.splice)||isArguments(value))){return!value.length;}return!nativeKeys(value).length;}/**
   * Performs a deep comparison between two values to determine if they are
   * equivalent.
   *
   * **Note:** This method supports comparing arrays, array buffers, booleans,
   * date objects, error objects, maps, numbers, `Object` objects, regexes,
   * sets, strings, symbols, and typed arrays. `Object` objects are compared
   * by their own, not inherited, enumerable properties. Functions and DOM
   * nodes are **not** supported.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to compare.
   * @param {*} other The other value to compare.
   * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
   * @example
   *
   * var object = { 'a': 1 };
   * var other = { 'a': 1 };
   *
   * _.isEqual(object, other);
   * // => true
   *
   * object === other;
   * // => false
   */function isEqual(value,other){return baseIsEqual(value,other);}/**
   * Checks if `value` is a finite primitive number.
   *
   * **Note:** This method is based on
   * [`Number.isFinite`](https://mdn.io/Number/isFinite).
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a finite number, else `false`.
   * @example
   *
   * _.isFinite(3);
   * // => true
   *
   * _.isFinite(Number.MIN_VALUE);
   * // => true
   *
   * _.isFinite(Infinity);
   * // => false
   *
   * _.isFinite('3');
   * // => false
   */function isFinite(value){return typeof value=='number'&&nativeIsFinite(value);}/**
   * Checks if `value` is classified as a `Function` object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a function, else `false`.
   * @example
   *
   * _.isFunction(_);
   * // => true
   *
   * _.isFunction(/abc/);
   * // => false
   */function isFunction(value){if(!isObject(value)){return false;}// The use of `Object#toString` avoids issues with the `typeof` operator
// in Safari 9 which returns 'object' for typed arrays and other constructors.
var tag=baseGetTag(value);return tag==funcTag||tag==genTag||tag==asyncTag||tag==proxyTag;}/**
   * Checks if `value` is a valid array-like length.
   *
   * **Note:** This method is loosely based on
   * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
   * @example
   *
   * _.isLength(3);
   * // => true
   *
   * _.isLength(Number.MIN_VALUE);
   * // => false
   *
   * _.isLength(Infinity);
   * // => false
   *
   * _.isLength('3');
   * // => false
   */function isLength(value){return typeof value=='number'&&value>-1&&value%1==0&&value<=MAX_SAFE_INTEGER;}/**
   * Checks if `value` is the
   * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
   * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is an object, else `false`.
   * @example
   *
   * _.isObject({});
   * // => true
   *
   * _.isObject([1, 2, 3]);
   * // => true
   *
   * _.isObject(_.noop);
   * // => true
   *
   * _.isObject(null);
   * // => false
   */function isObject(value){var type=typeof value==='undefined'?'undefined':_typeof(value);return value!=null&&(type=='object'||type=='function');}/**
   * Checks if `value` is object-like. A value is object-like if it's not `null`
   * and has a `typeof` result of "object".
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
   * @example
   *
   * _.isObjectLike({});
   * // => true
   *
   * _.isObjectLike([1, 2, 3]);
   * // => true
   *
   * _.isObjectLike(_.noop);
   * // => false
   *
   * _.isObjectLike(null);
   * // => false
   */function isObjectLike(value){return value!=null&&(typeof value==='undefined'?'undefined':_typeof(value))=='object';}/**
   * Checks if `value` is `NaN`.
   *
   * **Note:** This method is based on
   * [`Number.isNaN`](https://mdn.io/Number/isNaN) and is not the same as
   * global [`isNaN`](https://mdn.io/isNaN) which returns `true` for
   * `undefined` and other non-number values.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is `NaN`, else `false`.
   * @example
   *
   * _.isNaN(NaN);
   * // => true
   *
   * _.isNaN(new Number(NaN));
   * // => true
   *
   * isNaN(undefined);
   * // => true
   *
   * _.isNaN(undefined);
   * // => false
   */function isNaN(value){// An `NaN` primitive is the only value that is not equal to itself.
// Perform the `toStringTag` check first to avoid errors with some
// ActiveX objects in IE.
return isNumber(value)&&value!=+value;}/**
   * Checks if `value` is `null`.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is `null`, else `false`.
   * @example
   *
   * _.isNull(null);
   * // => true
   *
   * _.isNull(void 0);
   * // => false
   */function isNull(value){return value===null;}/**
   * Checks if `value` is classified as a `Number` primitive or object.
   *
   * **Note:** To exclude `Infinity`, `-Infinity`, and `NaN`, which are
   * classified as numbers, use the `_.isFinite` method.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a number, else `false`.
   * @example
   *
   * _.isNumber(3);
   * // => true
   *
   * _.isNumber(Number.MIN_VALUE);
   * // => true
   *
   * _.isNumber(Infinity);
   * // => true
   *
   * _.isNumber('3');
   * // => false
   */function isNumber(value){return typeof value=='number'||isObjectLike(value)&&baseGetTag(value)==numberTag;}/**
   * Checks if `value` is classified as a `RegExp` object.
   *
   * @static
   * @memberOf _
   * @since 0.1.0
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a regexp, else `false`.
   * @example
   *
   * _.isRegExp(/abc/);
   * // => true
   *
   * _.isRegExp('/abc/');
   * // => false
   */var isRegExp=baseIsRegExp;/**
   * Checks if `value` is classified as a `String` primitive or object.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is a string, else `false`.
   * @example
   *
   * _.isString('abc');
   * // => true
   *
   * _.isString(1);
   * // => false
   */function isString(value){return typeof value=='string'||!isArray(value)&&isObjectLike(value)&&baseGetTag(value)==stringTag;}/**
   * Checks if `value` is `undefined`.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Lang
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is `undefined`, else `false`.
   * @example
   *
   * _.isUndefined(void 0);
   * // => true
   *
   * _.isUndefined(null);
   * // => false
   */function isUndefined(value){return value===undefined;}/**
   * Converts `value` to an array.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Lang
   * @param {*} value The value to convert.
   * @returns {Array} Returns the converted array.
   * @example
   *
   * _.toArray({ 'a': 1, 'b': 2 });
   * // => [1, 2]
   *
   * _.toArray('abc');
   * // => ['a', 'b', 'c']
   *
   * _.toArray(1);
   * // => []
   *
   * _.toArray(null);
   * // => []
   */function toArray(value){if(!isArrayLike(value)){return values(value);}return value.length?copyArray(value):[];}/**
   * Converts `value` to an integer.
   *
   * **Note:** This method is loosely based on
   * [`ToInteger`](http://www.ecma-international.org/ecma-262/7.0/#sec-tointeger).
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to convert.
   * @returns {number} Returns the converted integer.
   * @example
   *
   * _.toInteger(3.2);
   * // => 3
   *
   * _.toInteger(Number.MIN_VALUE);
   * // => 0
   *
   * _.toInteger(Infinity);
   * // => 1.7976931348623157e+308
   *
   * _.toInteger('3.2');
   * // => 3
   */var toInteger=Number;/**
   * Converts `value` to a number.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to process.
   * @returns {number} Returns the number.
   * @example
   *
   * _.toNumber(3.2);
   * // => 3.2
   *
   * _.toNumber(Number.MIN_VALUE);
   * // => 5e-324
   *
   * _.toNumber(Infinity);
   * // => Infinity
   *
   * _.toNumber('3.2');
   * // => 3.2
   */var toNumber=Number;/**
   * Converts `value` to a string. An empty string is returned for `null`
   * and `undefined` values. The sign of `-0` is preserved.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @category Lang
   * @param {*} value The value to convert.
   * @returns {string} Returns the converted string.
   * @example
   *
   * _.toString(null);
   * // => ''
   *
   * _.toString(-0);
   * // => '-0'
   *
   * _.toString([1, 2, 3]);
   * // => '1,2,3'
   */function toString(value){if(typeof value=='string'){return value;}return value==null?'':value+'';}/*------------------------------------------------------------------------*//**
   * Assigns own enumerable string keyed properties of source objects to the
   * destination object. Source objects are applied from left to right.
   * Subsequent sources overwrite property assignments of previous sources.
   *
   * **Note:** This method mutates `object` and is loosely based on
   * [`Object.assign`](https://mdn.io/Object/assign).
   *
   * @static
   * @memberOf _
   * @since 0.10.0
   * @category Object
   * @param {Object} object The destination object.
   * @param {...Object} [sources] The source objects.
   * @returns {Object} Returns `object`.
   * @see _.assignIn
   * @example
   *
   * function Foo() {
   *   this.a = 1;
   * }
   *
   * function Bar() {
   *   this.c = 3;
   * }
   *
   * Foo.prototype.b = 2;
   * Bar.prototype.d = 4;
   *
   * _.assign({ 'a': 0 }, new Foo, new Bar);
   * // => { 'a': 1, 'c': 3 }
   */var assign=createAssigner(function(object,source){copyObject(source,nativeKeys(source),object);});/**
   * This method is like `_.assign` except that it iterates over own and
   * inherited source properties.
   *
   * **Note:** This method mutates `object`.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @alias extend
   * @category Object
   * @param {Object} object The destination object.
   * @param {...Object} [sources] The source objects.
   * @returns {Object} Returns `object`.
   * @see _.assign
   * @example
   *
   * function Foo() {
   *   this.a = 1;
   * }
   *
   * function Bar() {
   *   this.c = 3;
   * }
   *
   * Foo.prototype.b = 2;
   * Bar.prototype.d = 4;
   *
   * _.assignIn({ 'a': 0 }, new Foo, new Bar);
   * // => { 'a': 1, 'b': 2, 'c': 3, 'd': 4 }
   */var assignIn=createAssigner(function(object,source){copyObject(source,nativeKeysIn(source),object);});/**
   * This method is like `_.assignIn` except that it accepts `customizer`
   * which is invoked to produce the assigned values. If `customizer` returns
   * `undefined`, assignment is handled by the method instead. The `customizer`
   * is invoked with five arguments: (objValue, srcValue, key, object, source).
   *
   * **Note:** This method mutates `object`.
   *
   * @static
   * @memberOf _
   * @since 4.0.0
   * @alias extendWith
   * @category Object
   * @param {Object} object The destination object.
   * @param {...Object} sources The source objects.
   * @param {Function} [customizer] The function to customize assigned values.
   * @returns {Object} Returns `object`.
   * @see _.assignWith
   * @example
   *
   * function customizer(objValue, srcValue) {
   *   return _.isUndefined(objValue) ? srcValue : objValue;
   * }
   *
   * var defaults = _.partialRight(_.assignInWith, customizer);
   *
   * defaults({ 'a': 1 }, { 'b': 2 }, { 'a': 3 });
   * // => { 'a': 1, 'b': 2 }
   */var assignInWith=createAssigner(function(object,source,srcIndex,customizer){copyObject(source,keysIn(source),object,customizer);});/**
   * Creates an object that inherits from the `prototype` object. If a
   * `properties` object is given, its own enumerable string keyed properties
   * are assigned to the created object.
   *
   * @static
   * @memberOf _
   * @since 2.3.0
   * @category Object
   * @param {Object} prototype The object to inherit from.
   * @param {Object} [properties] The properties to assign to the object.
   * @returns {Object} Returns the new object.
   * @example
   *
   * function Shape() {
   *   this.x = 0;
   *   this.y = 0;
   * }
   *
   * function Circle() {
   *   Shape.call(this);
   * }
   *
   * Circle.prototype = _.create(Shape.prototype, {
   *   'constructor': Circle
   * });
   *
   * var circle = new Circle;
   * circle instanceof Circle;
   * // => true
   *
   * circle instanceof Shape;
   * // => true
   */function create(prototype,properties){var result=baseCreate(prototype);return properties==null?result:assign(result,properties);}/**
   * Assigns own and inherited enumerable string keyed properties of source
   * objects to the destination object for all destination properties that
   * resolve to `undefined`. Source objects are applied from left to right.
   * Once a property is set, additional values of the same property are ignored.
   *
   * **Note:** This method mutates `object`.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The destination object.
   * @param {...Object} [sources] The source objects.
   * @returns {Object} Returns `object`.
   * @see _.defaultsDeep
   * @example
   *
   * _.defaults({ 'a': 1 }, { 'b': 2 }, { 'a': 3 });
   * // => { 'a': 1, 'b': 2 }
   */var defaults=baseRest(function(args){args.push(undefined,assignInDefaults);return assignInWith.apply(undefined,args);});/**
   * Checks if `path` is a direct property of `object`.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The object to query.
   * @param {Array|string} path The path to check.
   * @returns {boolean} Returns `true` if `path` exists, else `false`.
   * @example
   *
   * var object = { 'a': { 'b': 2 } };
   * var other = _.create({ 'a': _.create({ 'b': 2 }) });
   *
   * _.has(object, 'a');
   * // => true
   *
   * _.has(object, 'a.b');
   * // => true
   *
   * _.has(object, ['a', 'b']);
   * // => true
   *
   * _.has(other, 'a');
   * // => false
   */function has(object,path){return object!=null&&hasOwnProperty.call(object,path);}/**
   * Creates an array of the own enumerable property names of `object`.
   *
   * **Note:** Non-object values are coerced to objects. See the
   * [ES spec](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
   * for more details.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The object to query.
   * @returns {Array} Returns the array of property names.
   * @example
   *
   * function Foo() {
   *   this.a = 1;
   *   this.b = 2;
   * }
   *
   * Foo.prototype.c = 3;
   *
   * _.keys(new Foo);
   * // => ['a', 'b'] (iteration order is not guaranteed)
   *
   * _.keys('hi');
   * // => ['0', '1']
   */var keys=nativeKeys;/**
   * Creates an array of the own and inherited enumerable property names of `object`.
   *
   * **Note:** Non-object values are coerced to objects.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Object
   * @param {Object} object The object to query.
   * @returns {Array} Returns the array of property names.
   * @example
   *
   * function Foo() {
   *   this.a = 1;
   *   this.b = 2;
   * }
   *
   * Foo.prototype.c = 3;
   *
   * _.keysIn(new Foo);
   * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
   */var keysIn=nativeKeysIn;/**
   * Creates an object composed of the picked `object` properties.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The source object.
   * @param {...(string|string[])} [props] The property identifiers to pick.
   * @returns {Object} Returns the new object.
   * @example
   *
   * var object = { 'a': 1, 'b': '2', 'c': 3 };
   *
   * _.pick(object, ['a', 'c']);
   * // => { 'a': 1, 'c': 3 }
   */var pick=flatRest(function(object,props){return object==null?{}:basePick(object,baseMap(props,toKey));});/**
   * This method is like `_.get` except that if the resolved value is a
   * function it's invoked with the `this` binding of its parent object and
   * its result is returned.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The object to query.
   * @param {Array|string} path The path of the property to resolve.
   * @param {*} [defaultValue] The value returned for `undefined` resolved values.
   * @returns {*} Returns the resolved value.
   * @example
   *
   * var object = { 'a': [{ 'b': { 'c1': 3, 'c2': _.constant(4) } }] };
   *
   * _.result(object, 'a[0].b.c1');
   * // => 3
   *
   * _.result(object, 'a[0].b.c2');
   * // => 4
   *
   * _.result(object, 'a[0].b.c3', 'default');
   * // => 'default'
   *
   * _.result(object, 'a[0].b.c3', _.constant('default'));
   * // => 'default'
   */function result(object,path,defaultValue){var value=object==null?undefined:object[path];if(value===undefined){value=defaultValue;}return isFunction(value)?value.call(object):value;}/**
   * Creates an array of the own enumerable string keyed property values of `object`.
   *
   * **Note:** Non-object values are coerced to objects.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Object
   * @param {Object} object The object to query.
   * @returns {Array} Returns the array of property values.
   * @example
   *
   * function Foo() {
   *   this.a = 1;
   *   this.b = 2;
   * }
   *
   * Foo.prototype.c = 3;
   *
   * _.values(new Foo);
   * // => [1, 2] (iteration order is not guaranteed)
   *
   * _.values('hi');
   * // => ['h', 'i']
   */function values(object){return object==null?[]:baseValues(object,keys(object));}/*------------------------------------------------------------------------*//**
   * Converts the characters "&", "<", ">", '"', and "'" in `string` to their
   * corresponding HTML entities.
   *
   * **Note:** No other characters are escaped. To escape additional
   * characters use a third-party library like [_he_](https://mths.be/he).
   *
   * Though the ">" character is escaped for symmetry, characters like
   * ">" and "/" don't need escaping in HTML and have no special meaning
   * unless they're part of a tag or unquoted attribute value. See
   * [Mathias Bynens's article](https://mathiasbynens.be/notes/ambiguous-ampersands)
   * (under "semi-related fun fact") for more details.
   *
   * When working with HTML you should always
   * [quote attribute values](http://wonko.com/post/html-escaping) to reduce
   * XSS vectors.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category String
   * @param {string} [string=''] The string to escape.
   * @returns {string} Returns the escaped string.
   * @example
   *
   * _.escape('fred, barney, & pebbles');
   * // => 'fred, barney, &amp; pebbles'
   */function escape(string){string=toString(string);return string&&reHasUnescapedHtml.test(string)?string.replace(reUnescapedHtml,escapeHtmlChar):string;}/*------------------------------------------------------------------------*//**
   * This method returns the first argument it receives.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Util
   * @param {*} value Any value.
   * @returns {*} Returns `value`.
   * @example
   *
   * var object = { 'a': 1 };
   *
   * console.log(_.identity(object) === object);
   * // => true
   */function identity(value){return value;}/**
   * Creates a function that invokes `func` with the arguments of the created
   * function. If `func` is a property name, the created function returns the
   * property value for a given element. If `func` is an array or object, the
   * created function returns `true` for elements that contain the equivalent
   * source properties, otherwise it returns `false`.
   *
   * @static
   * @since 4.0.0
   * @memberOf _
   * @category Util
   * @param {*} [func=_.identity] The value to convert to a callback.
   * @returns {Function} Returns the callback.
   * @example
   *
   * var users = [
   *   { 'user': 'barney', 'age': 36, 'active': true },
   *   { 'user': 'fred',   'age': 40, 'active': false }
   * ];
   *
   * // The `_.matches` iteratee shorthand.
   * _.filter(users, _.iteratee({ 'user': 'barney', 'active': true }));
   * // => [{ 'user': 'barney', 'age': 36, 'active': true }]
   *
   * // The `_.matchesProperty` iteratee shorthand.
   * _.filter(users, _.iteratee(['user', 'fred']));
   * // => [{ 'user': 'fred', 'age': 40 }]
   *
   * // The `_.property` iteratee shorthand.
   * _.map(users, _.iteratee('user'));
   * // => ['barney', 'fred']
   *
   * // Create custom iteratee shorthands.
   * _.iteratee = _.wrap(_.iteratee, function(iteratee, func) {
   *   return !_.isRegExp(func) ? iteratee(func) : function(string) {
   *     return func.test(string);
   *   };
   * });
   *
   * _.filter(['abc', 'def'], /ef/);
   * // => ['def']
   */var iteratee=baseIteratee;/**
   * Creates a function that performs a partial deep comparison between a given
   * object and `source`, returning `true` if the given object has equivalent
   * property values, else `false`.
   *
   * **Note:** The created function is equivalent to `_.isMatch` with `source`
   * partially applied.
   *
   * Partial comparisons will match empty array and empty object `source`
   * values against any array or object value, respectively. See `_.isEqual`
   * for a list of supported value comparisons.
   *
   * @static
   * @memberOf _
   * @since 3.0.0
   * @category Util
   * @param {Object} source The object of property values to match.
   * @returns {Function} Returns the new spec function.
   * @example
   *
   * var objects = [
   *   { 'a': 1, 'b': 2, 'c': 3 },
   *   { 'a': 4, 'b': 5, 'c': 6 }
   * ];
   *
   * _.filter(objects, _.matches({ 'a': 4, 'c': 6 }));
   * // => [{ 'a': 4, 'b': 5, 'c': 6 }]
   */function matches(source){return baseMatches(assign({},source));}/**
   * Adds all own enumerable string keyed function properties of a source
   * object to the destination object. If `object` is a function, then methods
   * are added to its prototype as well.
   *
   * **Note:** Use `_.runInContext` to create a pristine `lodash` function to
   * avoid conflicts caused by modifying the original.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Util
   * @param {Function|Object} [object=lodash] The destination object.
   * @param {Object} source The object of functions to add.
   * @param {Object} [options={}] The options object.
   * @param {boolean} [options.chain=true] Specify whether mixins are chainable.
   * @returns {Function|Object} Returns `object`.
   * @example
   *
   * function vowels(string) {
   *   return _.filter(string, function(v) {
   *     return /[aeiou]/i.test(v);
   *   });
   * }
   *
   * _.mixin({ 'vowels': vowels });
   * _.vowels('fred');
   * // => ['e']
   *
   * _('fred').vowels().value();
   * // => ['e']
   *
   * _.mixin({ 'vowels': vowels }, { 'chain': false });
   * _('fred').vowels();
   * // => ['e']
   */function mixin(object,source,options){var props=keys(source),methodNames=baseFunctions(source,props);if(options==null&&!(isObject(source)&&(methodNames.length||!props.length))){options=source;source=object;object=this;methodNames=baseFunctions(source,keys(source));}var chain=!(isObject(options)&&'chain'in options)||!!options.chain,isFunc=isFunction(object);baseEach(methodNames,function(methodName){var func=source[methodName];object[methodName]=func;if(isFunc){object.prototype[methodName]=function(){var chainAll=this.__chain__;if(chain||chainAll){var result=object(this.__wrapped__),actions=result.__actions__=copyArray(this.__actions__);actions.push({'func':func,'args':arguments,'thisArg':object});result.__chain__=chainAll;return result;}return func.apply(object,arrayPush([this.value()],arguments));};}});return object;}/**
   * Reverts the `_` variable to its previous value and returns a reference to
   * the `lodash` function.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Util
   * @returns {Function} Returns the `lodash` function.
   * @example
   *
   * var lodash = _.noConflict();
   */function noConflict(){if(root._===this){root._=oldDash;}return this;}/**
   * This method returns `undefined`.
   *
   * @static
   * @memberOf _
   * @since 2.3.0
   * @category Util
   * @example
   *
   * _.times(2, _.noop);
   * // => [undefined, undefined]
   */function noop(){}// No operation performed.
/**
   * Generates a unique ID. If `prefix` is given, the ID is appended to it.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Util
   * @param {string} [prefix=''] The value to prefix the ID with.
   * @returns {string} Returns the unique ID.
   * @example
   *
   * _.uniqueId('contact_');
   * // => 'contact_104'
   *
   * _.uniqueId();
   * // => '105'
   */function uniqueId(prefix){var id=++idCounter;return toString(prefix)+id;}/*------------------------------------------------------------------------*//**
   * Computes the maximum value of `array`. If `array` is empty or falsey,
   * `undefined` is returned.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Math
   * @param {Array} array The array to iterate over.
   * @returns {*} Returns the maximum value.
   * @example
   *
   * _.max([4, 2, 8, 6]);
   * // => 8
   *
   * _.max([]);
   * // => undefined
   */function max(array){return array&&array.length?baseExtremum(array,identity,baseGt):undefined;}/**
   * Computes the minimum value of `array`. If `array` is empty or falsey,
   * `undefined` is returned.
   *
   * @static
   * @since 0.1.0
   * @memberOf _
   * @category Math
   * @param {Array} array The array to iterate over.
   * @returns {*} Returns the minimum value.
   * @example
   *
   * _.min([4, 2, 8, 6]);
   * // => 2
   *
   * _.min([]);
   * // => undefined
   */function min(array){return array&&array.length?baseExtremum(array,identity,baseLt):undefined;}/*------------------------------------------------------------------------*/// Add methods that return wrapped values in chain sequences.
lodash.assignIn=assignIn;lodash.before=before;lodash.bind=bind;lodash.chain=chain;lodash.compact=compact;lodash.concat=concat;lodash.create=create;lodash.defaults=defaults;lodash.defer=defer;lodash.delay=delay;lodash.filter=filter;lodash.flatten=flatten;lodash.flattenDeep=flattenDeep;lodash.iteratee=iteratee;lodash.keys=keys;lodash.map=map;lodash.matches=matches;lodash.mixin=mixin;lodash.negate=negate;lodash.once=once;lodash.pick=pick;lodash.slice=slice;lodash.sortBy=sortBy;lodash.tap=tap;lodash.thru=thru;lodash.toArray=toArray;lodash.values=values;// Add aliases.
lodash.extend=assignIn;// Add methods to `lodash.prototype`.
mixin(lodash,lodash);/*------------------------------------------------------------------------*/// Add methods that return unwrapped values in chain sequences.
lodash.clone=clone;lodash.escape=escape;lodash.every=every;lodash.find=find;lodash.forEach=forEach;lodash.has=has;lodash.head=head;lodash.identity=identity;lodash.indexOf=indexOf;lodash.isArguments=isArguments;lodash.isArray=isArray;lodash.isBoolean=isBoolean;lodash.isDate=isDate;lodash.isEmpty=isEmpty;lodash.isEqual=isEqual;lodash.isFinite=isFinite;lodash.isFunction=isFunction;lodash.isNaN=isNaN;lodash.isNull=isNull;lodash.isNumber=isNumber;lodash.isObject=isObject;lodash.isRegExp=isRegExp;lodash.isString=isString;lodash.isUndefined=isUndefined;lodash.last=last;lodash.max=max;lodash.min=min;lodash.noConflict=noConflict;lodash.noop=noop;lodash.reduce=reduce;lodash.result=result;lodash.size=size;lodash.some=some;lodash.uniqueId=uniqueId;// Add aliases.
lodash.each=forEach;lodash.first=head;mixin(lodash,function(){var source={};baseForOwn(lodash,function(func,methodName){if(!hasOwnProperty.call(lodash.prototype,methodName)){source[methodName]=func;}});return source;}(),{'chain':false});/*------------------------------------------------------------------------*//**
   * The semantic version number.
   *
   * @static
   * @memberOf _
   * @type {string}
   */lodash.VERSION=VERSION;// Add `Array` methods to `lodash.prototype`.
baseEach(['pop','join','replace','reverse','split','push','shift','sort','splice','unshift'],function(methodName){var func=(/^(?:replace|split)$/.test(methodName)?String.prototype:arrayProto)[methodName],chainName=/^(?:push|sort|unshift)$/.test(methodName)?'tap':'thru',retUnwrapped=/^(?:pop|join|replace|shift)$/.test(methodName);lodash.prototype[methodName]=function(){var args=arguments;if(retUnwrapped&&!this.__chain__){var value=this.value();return func.apply(isArray(value)?value:[],args);}return this[chainName](function(value){return func.apply(isArray(value)?value:[],args);});};});// Add chain sequence methods to the `lodash` wrapper.
lodash.prototype.toJSON=lodash.prototype.valueOf=lodash.prototype.value=wrapperValue;/*--------------------------------------------------------------------------*/// Some AMD build optimizers, like r.js, check for condition patterns like:
if("function"=='function'&&_typeof(__webpack_require__(19))=='object'&&__webpack_require__(19)){// Expose Lodash on the global object to prevent errors when Lodash is
// loaded by a script tag in the presence of an AMD loader.
// See http://requirejs.org/docs/errors.html#mismatch for more details.
// Use `_.noConflict` to remove Lodash from the global object.
root._=lodash;// Define as an anonymous module so, through path mapping, it can be
// referenced as the "underscore" module.
!(__WEBPACK_AMD_DEFINE_RESULT__ = function(){return lodash;}.call(exports, __webpack_require__, exports, module), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));}// Check for `exports` after `define` in case a build optimizer adds it.
else if(freeModule){// Export for Node.js.
(freeModule.exports=lodash)._=lodash;// Export for CommonJS support.
freeExports._=lodash;}else{// Export to the global object.
root._=lodash;}}).call(undefined);
/* WEBPACK VAR INJECTION */}.call(exports, __webpack_require__(18), __webpack_require__(9)(module)))

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _merge=__webpack_require__(0);var _merge2=_interopRequireDefault(_merge);var _form=__webpack_require__(90);var _form2=_interopRequireDefault(_form);var _opts=__webpack_require__(64);var _opts2=_interopRequireDefault(_opts);var _statusBar=__webpack_require__(59);var _statusBar2=_interopRequireDefault(_statusBar);var _submit=__webpack_require__(60);var _submit2=_interopRequireDefault(_submit);var _text=__webpack_require__(42);var _text2=_interopRequireDefault(_text);var _password=__webpack_require__(40);var _password2=_interopRequireDefault(_password);var _fields=__webpack_require__(88);var _fields2=_interopRequireDefault(_fields);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}exports.install=function(Vue,globalOptions,customFields){var _this=this;customFields=customFields?customFields:{};console.log(customFields);var vfForm={render:_form2.default,props:{client:{type:Boolean,required:false,default:false},ajax:{type:Boolean,required:false,default:false},action:{type:String},method:{type:String,required:false,default:'POST'},validation:{type:Object,required:false,default:function _default(){return{};}},triggers:{type:Object,required:false,default:function _default(){return{};}},options:{type:Object,required:false,default:function _default(){return{};}}},created:function created(){if(!this.ajax&&!this.client){var payload=this.options.additionalPayload;for(var key in payload){this.additionalValues.push({name:key,value:payload[key]});}}this.registerInterfieldsRules();this.registerTriggers();},data:function data(){return{globalOptions:globalOptions?globalOptions:{},templates:_merge2.default.recursive(_fields2.default,customFields),isForm:true,fields:[],additionalValues:[],errors:[],relatedFields:{},triggeredFields:{},sending:false};},computed:{labelClass:__webpack_require__(63),fieldClass:__webpack_require__(61),hasErrors:__webpack_require__(62),server:function server(){return!_this.ajax&&!_this.client;},opts:_opts2.default,pristine:function pristine(){return this.fields.length==0;}},methods:{submit:__webpack_require__(76),formData:__webpack_require__(68),getField:__webpack_require__(69),showAllErrors:__webpack_require__(75),reinitForm:__webpack_require__(74),registerInterfieldsRules:__webpack_require__(72),registerTriggers:__webpack_require__(73),childrenOf:__webpack_require__(67),getStatusBar:__webpack_require__(70)}};Vue.component('vf-form',vfForm);Vue.component('vf-text',(0,_text2.default)());Vue.component('vf-email',__webpack_require__(37)());Vue.component('vf-number',__webpack_require__(39)());Vue.component('vf-password',(0,_password2.default)());Vue.component('vf-file',__webpack_require__(38)());Vue.component('vf-textarea',__webpack_require__(43)());Vue.component('vf-select',__webpack_require__(41)());Vue.component('vf-buttons-list',__webpack_require__(34)());Vue.component('vf-date',__webpack_require__(36)());Vue.component('vf-checkbox',__webpack_require__(35)());Vue.component('vf-status-bar',_statusBar2.default);Vue.component('vf-submit',_submit2.default);};

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

var __vue_exports__, __vue_options__

/* styles */
__webpack_require__(128)

/* script */
__vue_exports__ = __webpack_require__(112)

/* template */
var __vue_template__ = __webpack_require__(124)
__vue_options__ = __vue_exports__ = __vue_exports__ || {}
if (
  typeof __vue_exports__.default === "object" ||
  typeof __vue_exports__.default === "function"
) {
if (Object.keys(__vue_exports__).some(function (key) { return key !== "default" && key !== "__esModule" })) {console.error("named exports are not supported in *.vue files.")}
__vue_options__ = __vue_exports__ = __vue_exports__.default
}
if (typeof __vue_options__ === "function") {
  __vue_options__ = __vue_options__.options
}
__vue_options__.__file = "/Users/jan/Sites/FlexyAdmin/FlexyAdmin/sys/flexyadmin/assets/js/vue-components/flexyblocks.vue"
__vue_options__.render = __vue_template__.render
__vue_options__.staticRenderFns = __vue_template__.staticRenderFns

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-7db827ae", __vue_options__)
  } else {
    hotAPI.reload("data-v-7db827ae", __vue_options__)
  }
})()}
if (__vue_options__.functional) {console.error("[vue-loader] flexyblocks.vue: functional components are not supported and should be defined in plain js files using render functions.")}

module.exports = __vue_exports__


/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

var __vue_exports__, __vue_options__

/* styles */
__webpack_require__(125)

/* script */
__vue_exports__ = __webpack_require__(114)

/* template */
var __vue_template__ = __webpack_require__(121)
__vue_options__ = __vue_exports__ = __vue_exports__ || {}
if (
  typeof __vue_exports__.default === "object" ||
  typeof __vue_exports__.default === "function"
) {
if (Object.keys(__vue_exports__).some(function (key) { return key !== "default" && key !== "__esModule" })) {console.error("named exports are not supported in *.vue files.")}
__vue_options__ = __vue_exports__ = __vue_exports__.default
}
if (typeof __vue_options__ === "function") {
  __vue_options__ = __vue_options__.options
}
__vue_options__.__file = "/Users/jan/Sites/FlexyAdmin/FlexyAdmin/sys/flexyadmin/assets/js/vue-components/vue-grid.vue"
__vue_options__.render = __vue_template__.render
__vue_options__.staticRenderFns = __vue_template__.staticRenderFns

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-06b0788c", __vue_options__)
  } else {
    hotAPI.reload("data-v-06b0788c", __vue_options__)
  }
})()}
if (__vue_options__.functional) {console.error("[vue-loader] vue-grid.vue: functional components are not supported and should be defined in plain js files using render functions.")}

module.exports = __vue_exports__


/***/ },
/* 24 */
/***/ function(module, exports) {

"use strict";
'use strict';exports.byteLength=byteLength;exports.toByteArray=toByteArray;exports.fromByteArray=fromByteArray;var lookup=[];var revLookup=[];var Arr=typeof Uint8Array!=='undefined'?Uint8Array:Array;var code='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';for(var i=0,len=code.length;i<len;++i){lookup[i]=code[i];revLookup[code.charCodeAt(i)]=i;}revLookup['-'.charCodeAt(0)]=62;revLookup['_'.charCodeAt(0)]=63;function placeHoldersCount(b64){var len=b64.length;if(len%4>0){throw new Error('Invalid string. Length must be a multiple of 4');}// the number of equal signs (place holders)
// if there are two placeholders, than the two characters before it
// represent one byte
// if there is only one, then the three characters before it represent 2 bytes
// this is just a cheap hack to not do indexOf twice
return b64[len-2]==='='?2:b64[len-1]==='='?1:0;}function byteLength(b64){// base64 is 4/3 + up to two characters of the original data
return b64.length*3/4-placeHoldersCount(b64);}function toByteArray(b64){var i,j,l,tmp,placeHolders,arr;var len=b64.length;placeHolders=placeHoldersCount(b64);arr=new Arr(len*3/4-placeHolders);// if there are placeholders, only get up to the last complete 4 chars
l=placeHolders>0?len-4:len;var L=0;for(i=0,j=0;i<l;i+=4,j+=3){tmp=revLookup[b64.charCodeAt(i)]<<18|revLookup[b64.charCodeAt(i+1)]<<12|revLookup[b64.charCodeAt(i+2)]<<6|revLookup[b64.charCodeAt(i+3)];arr[L++]=tmp>>16&0xFF;arr[L++]=tmp>>8&0xFF;arr[L++]=tmp&0xFF;}if(placeHolders===2){tmp=revLookup[b64.charCodeAt(i)]<<2|revLookup[b64.charCodeAt(i+1)]>>4;arr[L++]=tmp&0xFF;}else if(placeHolders===1){tmp=revLookup[b64.charCodeAt(i)]<<10|revLookup[b64.charCodeAt(i+1)]<<4|revLookup[b64.charCodeAt(i+2)]>>2;arr[L++]=tmp>>8&0xFF;arr[L++]=tmp&0xFF;}return arr;}function tripletToBase64(num){return lookup[num>>18&0x3F]+lookup[num>>12&0x3F]+lookup[num>>6&0x3F]+lookup[num&0x3F];}function encodeChunk(uint8,start,end){var tmp;var output=[];for(var i=start;i<end;i+=3){tmp=(uint8[i]<<16)+(uint8[i+1]<<8)+uint8[i+2];output.push(tripletToBase64(tmp));}return output.join('');}function fromByteArray(uint8){var tmp;var len=uint8.length;var extraBytes=len%3;// if we have 1 byte left, pad 2 bytes
var output='';var parts=[];var maxChunkLength=16383;// must be multiple of 3
// go through the array every three bytes, we'll deal with trailing stuff later
for(var i=0,len2=len-extraBytes;i<len2;i+=maxChunkLength){parts.push(encodeChunk(uint8,i,i+maxChunkLength>len2?len2:i+maxChunkLength));}// pad the end with zeros, but make sure to not forget the extra bytes
if(extraBytes===1){tmp=uint8[len-1];output+=lookup[tmp>>2];output+=lookup[tmp<<4&0x3F];output+='==';}else if(extraBytes===2){tmp=(uint8[len-2]<<8)+uint8[len-1];output+=lookup[tmp>>10];output+=lookup[tmp>>4&0x3F];output+=lookup[tmp<<2&0x3F];output+='=';}parts.push(output);return parts.join('');}

/***/ },
/* 25 */
/***/ function(module, exports) {

"use strict";
"use strict";exports.read=function(buffer,offset,isLE,mLen,nBytes){var e,m;var eLen=nBytes*8-mLen-1;var eMax=(1<<eLen)-1;var eBias=eMax>>1;var nBits=-7;var i=isLE?nBytes-1:0;var d=isLE?-1:1;var s=buffer[offset+i];i+=d;e=s&(1<<-nBits)-1;s>>=-nBits;nBits+=eLen;for(;nBits>0;e=e*256+buffer[offset+i],i+=d,nBits-=8){}m=e&(1<<-nBits)-1;e>>=-nBits;nBits+=mLen;for(;nBits>0;m=m*256+buffer[offset+i],i+=d,nBits-=8){}if(e===0){e=1-eBias;}else if(e===eMax){return m?NaN:(s?-1:1)*Infinity;}else{m=m+Math.pow(2,mLen);e=e-eBias;}return(s?-1:1)*m*Math.pow(2,e-mLen);};exports.write=function(buffer,value,offset,isLE,mLen,nBytes){var e,m,c;var eLen=nBytes*8-mLen-1;var eMax=(1<<eLen)-1;var eBias=eMax>>1;var rt=mLen===23?Math.pow(2,-24)-Math.pow(2,-77):0;var i=isLE?0:nBytes-1;var d=isLE?1:-1;var s=value<0||value===0&&1/value<0?1:0;value=Math.abs(value);if(isNaN(value)||value===Infinity){m=isNaN(value)?1:0;e=eMax;}else{e=Math.floor(Math.log(value)/Math.LN2);if(value*(c=Math.pow(2,-e))<1){e--;c*=2;}if(e+eBias>=1){value+=rt/c;}else{value+=rt*Math.pow(2,1-eBias);}if(value*c>=2){e++;c/=2;}if(e+eBias>=eMax){m=0;e=eMax;}else if(e+eBias>=1){m=(value*c-1)*Math.pow(2,mLen);e=e+eBias;}else{m=value*Math.pow(2,eBias-1)*Math.pow(2,mLen);e=0;}}for(;mLen>=8;buffer[offset+i]=m&0xff,i+=d,m/=256,mLen-=8){}e=e<<mLen|m;eLen+=mLen;for(;eLen>0;buffer[offset+i]=e&0xff,i+=d,e/=256,eLen-=8){}buffer[offset+i-d]|=s*128;};

/***/ },
/* 26 */
/***/ function(module, exports) {

"use strict";
'use strict';var toString={}.toString;module.exports=Array.isArray||function(arr){return toString.call(arr)=='[object Array]';};

/***/ },
/* 27 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){if(this.errors.length)return"remove";if(this.success)return"ok";return"";};

/***/ },
/* 28 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){var fieldClass="VF-Field--"+ucfirst(this.fieldType);var str='';var classes={'VF-Field--required':this.required||this.Rules.required||this.isRequired,'VF-Field--disabled':this.disabled,'has-error':this.errors.length,'has-feedback':this.hasFeedback,'has-success':this.success};classes[fieldClass]=true;for(var c in classes){if(classes[c])str+=' '+c;}return str;};function ucfirst(string){return string.charAt(0).toUpperCase()+string.slice(1);}

/***/ },
/* 29 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.errors.length||this.success;};

/***/ },
/* 30 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.label&&!this.hideLabel;};

/***/ },
/* 31 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.hadErrors&&!this.errors.length;};

/***/ },
/* 32 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){var triggers=this.getForm().triggers;return triggers.hasOwnProperty(this.name)?triggers[this.name]:false;};

/***/ },
/* 33 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return this.errors.length?this.getMessage(this.errors[0]):'';};

/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{data:function data(){return{fieldType:'buttons',filteringField:null,allSelected:false};},props:{items:{type:Array,required:true},multiple:{type:Boolean,required:false,default:false},toggleTexts:{default:false},value:{required:false,default:function _default(){return[];}},filterBy:{type:String,default:''}},ready:function ready(){if(!this.toggleTexts){var inForm=this.inForm();var texts=this.getForm().options.texts;this.toggleTexts={select:inForm?texts.selectAll:'Select All',unselect:inForm?texts.unselectAll:'Unselect All'};}if(this.filterBy){this.filteringField=this.getField(this.filterBy);this.$watch('filterValue',function(val){if(val){this.value=this.multiple?[]:'';}}.bind(this));}},computed:{type:function type(){return this.multiple?"checkbox":"radio";},filterValue:function filterValue(){return this.filteringField?this.filteringField.value:null;},toggleText:function toggleText(){return this.allSelected?this.toggleTexts.unselect:this.toggleTexts.select;},arraySymbol:__webpack_require__(13)},methods:{updateValue:function updateValue(val,e){var checked=e.target.checked;if(this.multiple){if(checked){this.curValue.push(val);}else{this.curValue=this.curValue.filter(function(value){return value!=val;});}}else{this.curValue=val;}},isChecked:function isChecked(value){return this.multiple?this.curValue.indexOf(value)>-1:value==this.curValue;},reset:function reset(){this.wasReset=true;this.curValue=this.multiple?[]:'';},passesFilter:function passesFilter(item){if(!this.filterBy||!this.filterValue)return true;return item[this.filterBy]==this.filterValue;},toggle:function toggle(){this.allSelected=!this.allSelected;if(this.allSelected){this.items.forEach(function(item){if(this.passesFilter(item))this.value.push(item.id);}.bind(this));}else{this.value=[];}}}});};

/***/ },
/* 35 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{props:{checked:{type:Boolean,default:undefined}},created:function created(){this.wasReset=true;this.curValue=this.checked;},mounted:function mounted(){// if (typeof this.checked=='undefined') {
//   this.dirty = true;
// }
},methods:{updateValue:function updateValue(e){this.curValue=e.target.checked;},reset:function reset(){this.wasReset=true;this.checked=undefined;}},data:function data(){return{initialValue:this.checked,fieldType:'checkbox'};}});};

/***/ },
/* 36 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};var merge=__webpack_require__(0);var Field=__webpack_require__(1);var clone=__webpack_require__(2);var convertDateRulesToMoment=__webpack_require__(65);var DATE_FORMAT='YYYY-MM-DD HH:mm:ss';module.exports=function(){return merge.recursive(Field(),{data:function data(){return{fieldType:'date',datepicker:null};},props:{placeholder:{type:String,required:false,default:'Select Date'},noInput:{type:Boolean,default:false},format:{type:String,required:false,default:'DD/MM/YYYY'},range:{type:Boolean,required:false,default:false},options:{type:Object,required:false,default:function _default(){return{};}},clearLabel:{type:String,required:false,default:'Clear'}},created:function created(){this.rules=convertDateRulesToMoment(this.rules);this.$watch('rules',function(){this.rules=convertDateRulesToMoment(this.rules);},{deep:true});},mounted:function mounted(){if(typeof $=='undefined'){console.error('vue-form-2: missing global dependency: vf-date depends on JQuery');return;}this.datepicker=$(this.$el).find(".VF-Field--Date__datepicker");if(typeof this.datepicker.daterangepicker=='undefined'){console.error('vue-form-2: missing global dependency: vf-date depends on daterangepicker');return;}var dateRule=this.range?'daterange':'date';this.Rules[dateRule]=true;var parentOptions=this.inForm()?clone(this.getForm().options.dateOptions):{};if(this.disabled)return;this.opts=merge.recursive(parentOptions,this.opts);var options=merge.recursive(this.opts,{singleDatePicker:!this.range,format:this.format,locale:{cancelLabel:this.clearLabel}});if(this.value){options=merge.recursive(options,{startDate:this.range?this.value.start:this.value,endDate:this.range?this.value.end:this.value});}this.datepicker.daterangepicker(options);this.datepicker.on('apply.daterangepicker',function(ev,picker){this.curValue=this.range?{start:picker.startDate,end:picker.endDate}:picker.startDate;if(!this.noInput){this.injectValueToField(this.curValue);}}.bind(this));this.datepicker.on('cancel.daterangepicker',function(ev,picker){this.curValue=null;this.datepicker.data('daterangepicker').setStartDate(moment().format(this.format));this.datepicker.data('daterangepicker').setEndDate(moment().format(this.format));this.datepicker.trigger("change");}.bind(this));if(!this.range&&!this.isTimepicker){this.datepicker.on('show.daterangepicker',function(ev,picker){var el=$(picker.container[0]);el.find(".ranges").eq(0).show().css("display","block !important");el.find(".daterangepicker_start_input").hide();el.find(".daterangepicker_end_input").hide();el.find(".applyBtn").hide();}.bind(this));}},methods:{injectValueToField:function injectValueToField(val){if(this.range){var formatted=val.start.format(this.format)+" - "+val.end.format(this.format);this.datepicker.find("input").val(formatted);var start=val.start.isValid()?val.start:moment();var end=val.end.isValid()?val.end:moment();this.setDatepickerValue({start:start,end:end});}else{var _formatted=val.format(this.format);this.datepicker.find("input").val(_formatted);var pickerDate=val.isValid()?val:moment();this.setDatepickerValue(pickerDate);}},updateValue:function updateValue(e){var value=e.target.value;if(!value.trim()){this.curValue='';return;}var val=this.momentizeValue(value);this.curValue=val;this.injectValueToField(val);},isValidMoment:function isValidMoment(val){if(this.range){return val&&val.start&&this.isMoment(val.start)&&val.start.isValid()&&val.end&&this.isMoment(val.end)&&val.end.isValid();}return val&&this.isMoment(val)&&val.isValid();},isMoment:function isMoment(val){return val&&(typeof val==='undefined'?'undefined':_typeof(val))=='object'&&val.hasOwnProperty('_isAMomentObject');},momentizeValue:function momentizeValue(val){if(this.isValidMoment(val))return val;if(!val)val=this.curValue;if(this.range&&typeof val=='string'){var pieces=val.split('-');val={};val.start=pieces[0];val.end=pieces[1];}return this.range?{start:moment(val.start,this.format),end:moment(val.end,this.format)}:moment(val,this.format);},setValue:function setValue(val){try{if(this.range){if(typeof val.start=='string')val.start=moment(val.start,DATE_FORMAT);if(typeof val.end=='string')val.end=moment(val.end,DATE_FORMAT);}else{if(typeof val=='string')val=moment(val,DATE_FORMAT);}if(!this.isValidMoment(val))throw'invalid date';}catch(e){var error='vue-form-2: invalid date passed to field "'+this.name+'".';error+=this.range?'Date range must be passed as an object with \'start\' and \'end\' properties, each being a moment object or conforming to the '+DATE_FORMAT+' format.':'Date must be either a valid moment object or a string conforming to the '+DATE_FORMAT+' format.';console.error(error);return;}this.curValue=val;setTimeout(function(){this.setDatepickerValue(this.curValue);}.bind(this),0);this.dirty=true;},reset:function reset(){this.wasReset=true;this.curValue=null;this.setDatepickerValue(moment());this.datepicker.trigger("change");},setDatepickerValue:function setDatepickerValue(value){var start=this.range?value.start:value;var end=this.range?value.end:value;this.datepicker.data('daterangepicker').setStartDate(start);this.datepicker.data('daterangepicker').setEndDate(end);}},computed:{type:function type(){return this.noInput?'span':'input';},isTimepicker:function isTimepicker(){return this.options.hasOwnProperty('timePicker')&&this.options.timePicker;},formattedDate:function formattedDate(){if(!this.curValue||!this.range&&(!this.curValue.format||this.curValue.format()=='Invalid date')||this.range&&(!this.curValue.start||!this.curValue.start.format||!this.curValue.end||!this.curValue.end.format)){return this.noInput?this.placeholder:'';}if(!this.range)return this.curValue.format(this.format);return this.curValue.start.format(this.format)+" - "+this.curValue.end.format(this.format);},serverFormat:function serverFormat(){if(!this.curValue||isDateString(this.curValue))return'';if(!this.range)return this.curValue.format();return JSON.stringify({start:this.curValue.start.format(),end:this.curValue.end.format()});}}});};function isDateString(value){return value&&(typeof value=='string'||value.hasOwnProperty('start')&&typeof value.start=='string');}

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var Input=__webpack_require__(5);module.exports=function(){return merge.recursive(Input(),{data:function data(){return{fieldType:'email'};},mounted:function mounted(){this.Rules.email=true;}});};

/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var clone=__webpack_require__(2);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{data:function data(){return{fieldType:'file'};},props:{options:{type:Object,required:false,default:function _default(){return{};}},ajax:{type:Boolean},dest:{type:String,default:'/'},done:{type:Function},url:{type:String},error:{type:Function},valueKey:{type:String,default:'value'}},mounted:function mounted(){if(!this.ajax)return;if(typeof $=='undefined'){console.error('vue-form-2: missing global dependency: vf-file with ajax depends on JQuery');return;}if(typeof $(this.$el).fileupload=='undefined'){console.error('vue-form-2: missing global dependency: vf-file with ajax depends on the jQuery-File-Upload plugin');return;}var self=this;var parentOptions=this.inForm()?clone(this.getForm().options.fileOptions):{};var options=merge.recursive(parentOptions,this.options);if(this.url)options.url=this.url;if(!options.hasOwnProperty("formData"))options.formData={};options.formData.rules=JSON.stringify(this.rules);if(!options.formData.hasOwnProperty('dest')){options.formData.dest=this.dest;}if(!options.hasOwnProperty('done')){options.done=this.done?this.done:function(e,data){self.setValue(data.result[self.valueKey]);};}if(!options.hasOwnProperty('error')){options.error=this.error?this.error:function(e,data){bootbox.alert(e.responseJSON.error.message);};}$(this.$el).find("input[type=file]").fileupload(options);}});};

/***/ },
/* 39 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var Input=__webpack_require__(5);module.exports=function(){return merge.recursive(Input(),{data:function data(){return{fieldType:'number'};},ready:function ready(){this.$set('rules.number',true);}});};

/***/ },
/* 40 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(){return _merge2.default.recursive((0,_input2.default)(),{data:function data(){return{fieldType:'password'};}});};var _merge=__webpack_require__(0);var _merge2=_interopRequireDefault(_merge);var _input=__webpack_require__(5);var _input2=_interopRequireDefault(_input);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}

/***/ },
/* 41 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var clone=__webpack_require__(2);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{props:{items:{type:Array,required:false,default:function _default(){return[];}},multiple:{type:Boolean,required:false,default:false},select2:{type:Boolean},options:{type:Object,default:function _default(){return{};}},containerClass:{required:false},placeholder:{type:String,required:false,default:'Select Option'},noDefault:{type:Boolean},filterBy:{type:String,default:''},ajaxUrl:{type:String,default:''},callback:{type:Function,required:false},html:{type:Boolean}},mounted:function mounted(){var that=this;var value;var callback=this.callback;var filterBy=this.filterBy;if(this.filterBy){this.$watch('filterValue',function(val){if(val){if(this.select2&&!this.ajaxUrl)this.el.select2(options);this.reset();}}.bind(this));}if(this.select2||this.ajaxUrl){if(typeof $=='undefined'){console.error('vue-form-2: missing global dependency: vf-select with select2 depends on JQuery');return;}if(typeof $(this.$el).select2=='undefined'){console.error('vue-form-2: missing global dependency: vf-select with select2 depends on Select2');return;}var options=this.inForm()?clone(this.getForm().options.select2Options):{};options=merge.recursive(options,{placeholder:this.placeholder});if(!this.html&&!this.filterBy)options.data=this.items;if(this.ajaxUrl){options=merge.recursive(options,{ajax:{url:this.ajaxUrl,dataType:'json',delay:250,data:function data(params){var query={q:params.term,selected:that.curValue};if(filterBy){var filterValue=$("[name="+filterBy+"]").val();if(filterValue)query[filterBy]=filterValue;}return query;},processResults:function processResults(data){return{results:callback?$.map(data,callback):data};},cache:true},minimumInputLength:3});}options=merge.recursive(options,this.opts);this.el=$(this.$el).find("select");this.el.select2(options).on("select2:select",function(e){that.curValue=$(this).select2('val');}).on("select2:unselecting",function(e){if(that.multiple){var $this=$(this);setTimeout(function(){value=$this.select2('val');that.curValue=value?value:[];},0);}else{that.value='';}});if(this.value){this.el.val(this.value).trigger("change");}setTimeout(function(){this.el.trigger('change');}.bind(this),0);if(this.containerClass){this.el.data('select2').$container.addClass("container-"+this.containerClass);this.el.data('select2').$dropdown.addClass("dropdown-"+this.containerClass);}}},computed:{arraySymbol:__webpack_require__(13),filterValue:function filterValue(){if(!this.filterBy)return'';return this.getField(this.filterBy).curValue;}},data:function data(){return{fieldType:'select',tagName:'select'};},methods:{setValue:function setValue(value){this.curValue=value;this.dirty=true;if(this.select2&&this.el)this.el.val(value).trigger("change");},reset:function reset(){var value=this.multiple?[]:'';this.wasReset=true;this.curValue=value;if(this.select2&&this.el)this.el.val(value).trigger("change");}}});};

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var merge=__webpack_require__(0);var Input=__webpack_require__(5);module.exports=function(){return merge.recursive(Input(),{data:function data(){return{fieldType:'text'};}});};

/***/ },
/* 43 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _updateValue=__webpack_require__(14);var _updateValue2=_interopRequireDefault(_updateValue);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}var merge=__webpack_require__(0);var clone=__webpack_require__(2);var Field=__webpack_require__(1);module.exports=function(){return merge.recursive(Field(),{props:{placeholder:{type:String,required:false,default:''},disabled:{type:Boolean},tinymce:{type:Boolean},options:{type:Object,default:function _default(){return{};}},debounce:{type:Number,default:300}},data:function data(){return{editor:null,fieldType:'textarea',tagName:'textarea'};},methods:{updateValue:_updateValue2.default},mounted:function mounted(){if(!this.tinymce)return;if(typeof tinymce=='undefined'){console.error('vue-form-2: missing global dependency. TinyMCE is required on a vf-textarea with a tinymce prop');return;}var that=this;var _setup=that.options&&that.options.hasOwnProperty('setup')?that.options.setup:function(){};var parentSetup=that.getForm().opts.tinymceOptions.hasOwnProperty('setup')?that.getForm().opts.tinymceOptions.setup:function(){};var options=merge.recursive(clone(this.getForm().opts.tinymceOptions),this.options);options=merge.recursive(options,{selector:'textarea[name='+this.name+']',setup:function setup(ed){that.editor=ed;parentSetup(ed);_setup(ed);ed.on('change',function(e){that.curValue=ed.getContent();}.bind(this));}});tinymce.init(options);this.$watch('curValue',function(val){if(val!=tinymce.get("textarea_"+this.name).getContent()){tinymce.get("textarea_"+this.name).setContent(val);}});}});};

/***/ },
/* 44 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(item,show,rule){var unique=true;this.getForm().errors.forEach(function(error,i){if(error.name==item.name&&error.rule==rule){this.getForm().errors[i].show=show;unique=false;}}.bind(this));if(unique)this.getForm().errors.push(item);};

/***/ },
/* 45 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(name){if(this.$root.$refs.hasOwnProperty(name))return this.$root.$refs[name];return this.getForm().getField(name);};

/***/ },
/* 46 */
/***/ function(module, exports) {

"use strict";
"use strict";var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(rule){var messages=this.getForm().opts.messages;var message=this.messages[rule]?this.messages[rule]:messages[rule];if((typeof message==="undefined"?"undefined":_typeof(message))=='object'){message=extractMessage(rule,message,this);}var params=this.Rules[rule];if(isMomentObject(params)){message=message.replace("{0}",params.format(this.format));}else if(Array.isArray(params)){params.forEach(function(param,index){message=message.replace("{"+index+"}",!isMomentObject(param)?param:param.format(this.format));}.bind(this));}else if(typeof params=='number'||typeof params=='string'){message=message.replace("{0}",params);if(typeof params=='string'){var relatedField=this.getField(params);if(relatedField)message=message.replace(":relatedField",stripLabel(relatedField.label));}}message=message.replace(":field",this.label?stripLabel(this.label):this.name);if(this.fieldType=='date')message=message.replace(":format",this.format);return message;};function extractMessage(rule,message,field){if(field.Rules.number||field.Rules.integer)return message.number;if(rule=='between'&&_typeof(field.Rules[rule][0])=='object'||['min','max'].indexOf(rule)>-1&&_typeof(field.Rules[rule])=='object')return message.date;return message.string;}function stripLabel(label){return label.replace(/<(?:.|\n)*?>/gm,'');}function isMomentObject(param){return typeof param.isValid!='undefined';}if(!Array.isArray){Array.isArray=function(arg){return Object.prototype.toString.call(arg)==='[object Array]';};}

/***/ },
/* 47 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){if(typeof this.triggeredFields!='undefined'){this.triggeredFields.forEach(function(field){field.triggerOn();});}};

/***/ },
/* 48 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.getForm()&&this.getForm().isForm;};

/***/ },
/* 49 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(item){var index;this.getForm().errors.forEach(function(error,i){if(error.name==item.name&&error.rule==item.rule){index=i;}});if(index>=0)this.getForm().errors.splice(index,1);};

/***/ },
/* 50 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
"use strict";var isTriggerOn=__webpack_require__(16);module.exports=function(){if(!this.trigger){this.shouldShow=true;return;}var params=this.trigger.split(":");var triggerName=params[0];var values=params.length>1?params[1]:false;this.shouldShow=isTriggerOn(this,this.getField(triggerName),values);};

/***/ },
/* 51 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){if(!this.rules.remote)return;var formError={name:this.name,rule:"remote",show:true};this.$http.get(this.rules.remote,{value:this.value}).then(function(data){var valid=data.data;if(valid){this.errors.$remove('remote');if(this.inForm())this.removeFormError(formError);}else{if(this.errors.indexOf("remote")==-1)this.errors.push("remote");if(this.inForm())this.addFormError(formError,true);}});};

/***/ },
/* 52 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var validator={between:__webpack_require__(97),digits:__webpack_require__(100),email:__webpack_require__(101),greaterThan:__webpack_require__(102),smallerThan:__webpack_require__(110),integer:__webpack_require__(103),max:__webpack_require__(104),min:__webpack_require__(105),number:__webpack_require__(106),requiredIf:__webpack_require__(108),requiredAndShownIf:__webpack_require__(107),required:__webpack_require__(109),url:__webpack_require__(111),date:__webpack_require__(98),daterange:__webpack_require__(99)};var merge=__webpack_require__(0);function shouldShow(that,rule){return!that.pristine||['greaterThan','smallerThan'].indexOf(rule)>-1;}module.exports=function(){var formError;var isValid;validator=merge.recursive(validator,this.getForm().options.customRules);for(var rule in this.Rules){if(validator[rule]){isValid=!this.curValue&&rule!='required'&&rule!='requiredIf'&&rule!='requiredAndShownIf'||validator[rule](this);formError={name:this.name,rule:rule,show:shouldShow(this,rule)};if(isValid){this.errors=this.errors.filter(function(r){return r!=rule;});if(this.inForm())this.removeFormError(formError);}else{if(shouldShow(this,rule)){if(this.errors.indexOf(rule)==-1)this.errors.push(rule);}if(this.inForm()){this.addFormError(formError,!this.pristine,rule);}}}}if(this.errors.length)this.hadErrors=true;};

/***/ },
/* 53 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';module.exports={computed:{fieldClasses:__webpack_require__(28),hasFeedback:__webpack_require__(29),feedbackIcon:__webpack_require__(27),validationError:__webpack_require__(33),success:__webpack_require__(31),hasLabel:__webpack_require__(30),trigger:__webpack_require__(32)}};

/***/ },
/* 54 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var clone=__webpack_require__(2);module.exports={data:function data(){return{isField:true,curValue:'',tagName:'input',messages:{},isRequired:false,shouldShow:true,dirty:false,pristine:true,wasReset:false,initialValue:clone(this.value),hadErrors:false,errors:[],relatedFields:[],triggeredFields:[],Rules:{}};}};

/***/ },
/* 55 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';module.exports={methods:{getMessage:__webpack_require__(46),validateRemote:__webpack_require__(51),validate:__webpack_require__(52),addFormError:__webpack_require__(44),removeFormError:__webpack_require__(49),inForm:__webpack_require__(48),triggerOn:__webpack_require__(50),handleTriggeredFields:__webpack_require__(47),getForm:__webpack_require__(7),getField:__webpack_require__(45)}};

/***/ },
/* 56 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _merge=__webpack_require__(0);var _merge2=_interopRequireDefault(_merge);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}module.exports={mounted:function mounted(){var _this=this;if(this.value)this.setValue(this.value);if(this.required){this.Rules.required=true;}this.Rules=(0,_merge2.default)(this.Rules,this.rules);var inForm=this.inForm();if(inForm){(function(){var form=_this.getForm();if(!_this.getForm().options.sendOnlyDirtyFields)form.fields.push(_this);if(_this.getForm().options.sendOnlyDirtyFields){_this.$watch('dirty',function(isDirty){var _this2=this;if(isDirty){form.fields.push(this);}else{form.fields=form.fields.filter(function(field){return field.name!=_this2.name;});}}.bind(_this));}var v=_this.getForm().validation;if(v.rules&&v.rules.hasOwnProperty(_this.name)){_this.Rules=v.rules[_this.name];}if(typeof v.messages!='undefined'&&v.messages.hasOwnProperty(_this.name))_this.messages=v.messages[_this.name];setTimeout(function(){this.validate();}.bind(_this),0);if(form.relatedFields.hasOwnProperty(_this.name))_this.foreignFields=form.relatedFields[_this.name].map(function(name){return form.getField(name);});if(form.triggeredFields.hasOwnProperty(_this.name))_this.triggeredFields=form.triggeredFields[_this.name].map(function(name){return form.getField(name);});_this.handleTriggeredFields();})();}}};

/***/ },
/* 57 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports={props:{name:{type:String,required:true},value:{required:false,default:''},label:{type:String,required:false},hideLabel:{type:Boolean},disabled:{type:Boolean},required:{type:Boolean},rules:{type:Object,required:false,default:function _default(){return{};}}}};

/***/ },
/* 58 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _bus=__webpack_require__(4);var _bus2=_interopRequireDefault(_bus);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}var isEqual=__webpack_require__(66);exports.default={watch:{curValue:function curValue(newVal,oldVal){_bus2.default.$emit('vue-form.change::'+this.name,{name:this.name,value:newVal,oldValue:oldVal});_bus2.default.$emit('vue-form.change',{name:this.name,value:newVal,oldValue:oldVal});if(typeof this.foreignFields!='undefined'){this.foreignFields.forEach(function(field){field.validate();});}this.handleTriggeredFields();this.dirty=this.wasReset?false:!isEqual(this.curValue,this.initialValue);this.pristine=this.wasReset;this.wasReset=false;if(this.inForm())this.validate();}}};

/***/ },
/* 59 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _statusBar=__webpack_require__(93);var _statusBar2=_interopRequireDefault(_statusBar);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}module.exports={name:'status-bar',data:function data(){return{message:'',status:'success'};},render:_statusBar2.default,methods:{getForm:__webpack_require__(7),_setMessage:function _setMessage(message,status){this.message=message;this.status=status;},danger:function danger(message){this._setMessage(message,'danger');},success:function success(message){this._setMessage(message,'success');},info:function info(message){this._setMessage(message,'info');},warning:function warning(message){this._setMessage(message,'warning');},reset:function reset(){this.success('');},getErrorMessage:function getErrorMessage(error){var field=this.getForm().getField(error.name);if(error.hasOwnProperty('message'))return error.message.replace(":field",field.label);return field.getMessage(error.rule);}},computed:{Message:function Message(){return this.message;},Status:function Status(){if(this.hasErrors)return'danger';return this.status;},errorsCount:function errorsCount(){var texts=this.getForm().opts.texts;return this.showableErrors.length==1?texts.singleError:texts.errors.replace('{0}',this.showableErrors.length);},hasErrors:function hasErrors(){return!this.message&&this.getForm().opts.showClientErrorsInStatusBar&&this.showableErrors.length;},hasMessage:function hasMessage(){return!!this.Message||this.hasErrors;},showableErrors:function showableErrors(){var errors=[];this.getForm().errors.forEach(function(error){if(error.show)errors.push(error);});return errors;}}};

/***/ },
/* 60 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _submit=__webpack_require__(94);var _submit2=_interopRequireDefault(_submit);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}exports.default={render:_submit2.default,props:{text:{type:String,required:false,default:'Submit'}},methods:{getForm:__webpack_require__(7)},computed:{disabled:function disabled(){return this.getForm().sending||this.getForm().options.sendOnlyDirtyFields&&this.getForm().pristine;}}};

/***/ },
/* 61 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return this.opts.layout=='form-horizontal'?'col-sm-'+(12-this.opts.labelWidth):'';};

/***/ },
/* 62 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.errors.length>0;};

/***/ },
/* 63 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return this.opts.layout=='form-horizontal'?'col-sm-'+this.opts.labelWidth:'';};

/***/ },
/* 64 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(){var options=_merge2.default.recursive((0,_options2.default)(),this.globalOptions);return _merge2.default.recursive(options,this.options);};var _merge=__webpack_require__(0);var _merge2=_interopRequireDefault(_merge);var _options=__webpack_require__(80);var _options2=_interopRequireDefault(_options);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}

/***/ },
/* 65 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(rules){if(rules.min&&isDateString(rules.min))rules.min=moment(rules.min,'YYYY-MM-DD');if(rules.max&&isDateString(rules.max))rules.max=moment(rules.max,'YYYY-MM-DD');if(rules.between&&isDateString(rules.between[0])){rules.between[0]=moment(rules.between[0],'YYYY-MM-DD');rules.between[1]=moment(rules.between[1],'YYYY-MM-DD');}return rules;};function isDateString(str){return typeof str=='string'&&/\d{4}-\d{2}-\d{2}/.test(str);}

/***/ },
/* 66 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(value1,value2){if(value1&&value1.format)value1=value1.format('YYYY-MM-DD');if(value2&&value2.format)value2=value2.format('YYYY-MM-DD');if((typeof value1==='undefined'?'undefined':_typeof(value1))!='object'&&(typeof value2==='undefined'?'undefined':_typeof(value2))!='object')return value1==value2;var what=Object.prototype.toString;if(what.call(value1)=='[object Array]'){// multiple list
return arraysEqual(value1,value2);}if(value1&&value1.start&&value2&&value2.start){var value1start=value1.start.format?value1.start.format('YYYY-MM-DD'):value1.start;var value1end=value1.end.format?value1.end.format('YYYY-MM-DD'):value1.end;var value2start=value2.start.format?value2.start.format('YYYY-MM-DD'):value2.start;var value2end=value2.end.format?value2.end.format('YYYY-MM-DD'):value2.end;return value1start==value2start&&value1end==value2end;}return false;};function arraysEqual(arr1,arr2){var equal=true;if(arr1&&!arr2)return false;if(arr1.length!=arr2.length)return false;arr1.forEach(function(item){if((typeof item==='undefined'?'undefined':_typeof(item))!='object'&&arr2.indexOf(item)==-1)equal=false;});return equal;}

/***/ },
/* 67 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(ref){if(!this.$root.$refs.hasOwnProperty(ref))throw'vue-form: error in childrenOf method: ref "'+ref+'" was not found.';return getFields(this.$root.$refs[ref].$children,[]);};function getFields(children,result){children.forEach(function(child){if(child.hasOwnProperty('isField')&&child.isField){result.push(child);}else if(child.hasOwnProperty('$children')){getFields(child.$children,result);}});return result;}

/***/ },
/* 68 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};var merge=__webpack_require__(0);module.exports=function(){var data={};var value;this.fields.forEach(function(field){value=getValue(field.curValue);data[field.name]=value;});data=merge.recursive(data,this.opts.additionalPayload);return data;};function isValidMomentObject(value){return(typeof value==='undefined'?'undefined':_typeof(value))=='object'&&value.isValid&&value.isValid();}function isArray(value){return Object.prototype.toString.call(value)==='[object Array]';}function getValue(value){if(!value||(typeof value==='undefined'?'undefined':_typeof(value))!='object'||isArray(value))return value;if(isValidMomentObject(value))return value.format();if((typeof value==='undefined'?'undefined':_typeof(value))=='object'&&value.start&&isValidMomentObject(value.start)&&value.end&&isValidMomentObject(value.end)){return{start:value.start.format(),end:value.end.format()};}}

/***/ },
/* 69 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(name){if(this.$root.$refs.hasOwnProperty(name))return this.$root.$refs[name];return getField(this,name);};function getField(parent,name){if(parent.name==name){return parent;}else if(parent.$children&&parent.$children.length){var i;var result=null;for(i=0;result==null&&i<parent.$children.length;i++){result=getField(parent.$children[i],name);}return result;}return null;}

/***/ },
/* 70 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){return this.$root.$refs.statusbar;};

/***/ },
/* 71 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _ajax=__webpack_require__(77);var _ajax2=_interopRequireDefault(_ajax);var _client=__webpack_require__(78);var _client2=_interopRequireDefault(_client);var _server=__webpack_require__(79);var _server2=_interopRequireDefault(_server);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}module.exports=function(vm){if(vm.ajax)return(0,_ajax2.default)(vm);if(vm.client)return(0,_client2.default)(vm);return(0,_server2.default)(vm);};

/***/ },
/* 72 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){if(this.validation&&this.validation.rules){for(var field in this.validation.rules){for(var rule in this.validation.rules[field]){if(['requiredIf','requiredAndShownIf','smallerThan','greaterThan'].indexOf(rule)>-1){field=field.split(":")[0];var foreignField=this.validation.rules[field][rule].split(":")[0];if(typeof this.relatedFields[foreignField]=='undefined'){this.relatedFields[foreignField]=[];}this.relatedFields[foreignField].push(field);}}}}};

/***/ },
/* 73 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){for(var trigger in this.triggers){var triggerField=this.triggers[trigger].split(":")[0];if(typeof this.triggeredFields[triggerField]=='undefined'){this.triggeredFields[triggerField]=[];}this.triggeredFields[triggerField].push(trigger);}};

/***/ },
/* 74 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var clone=__webpack_require__(2);module.exports=function(){this.fields.forEach(function(field){field.initialValue=clone(field.curValue);field.dirty=false;});};

/***/ },
/* 75 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(){this.errors.forEach(function(error){var field=this.getField(error.name);if(field.errors.indexOf(error.rule)==-1){field.errors.push(error.rule);field.hadErrors=true;}error.show=true;}.bind(this));};

/***/ },
/* 76 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _bus=__webpack_require__(4);var _bus2=_interopRequireDefault(_bus);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}var getSubmitter=__webpack_require__(71);module.exports=function(e){if(e)e.preventDefault();if(this.errors.length>0)return handleErrors(this);if(this.sending||this.opts.sendOnlyDirtyFields&&this.pristine){return false;}var beforeSubmit=this.opts.beforeSubmit(this);if(typeof beforeSubmit=='boolean'&&beforeSubmit)return getSubmitter(this).submit(e);var resolveMethod=beforeSubmit.done?'done':'then';var rejectMethod=beforeSubmit.catch?'catch':'fail';beforeSubmit[resolveMethod](function(){return getSubmitter(this).submit(e);}.bind(this))[rejectMethod](function(){});};function handleErrors(vm){vm.showAllErrors();_bus2.default.$emit('vue-form.invalid.client',vm.errors);return false;}

/***/ },
/* 77 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(vm){return{submit:function submit(e){var status=vm.getStatusBar();if(e)e.preventDefault();vm.sending=true;status.info(vm.opts.texts.sending);_bus2.default.$emit('vue-form.sending');var data=vm.formData();var method=vm.method.toLowerCase();vm.$http[method](vm.action,getData(method,data)).then(function(data){vm.reinitForm();_bus2.default.$emit('vue-form.sent',data);vm.sending=false;status.success(typeof data.data=='string'?data.data:vm.opts.texts.sent);setTimeout(function(){status.reset();},vm.opts.successTimeout);}).catch(function(response){_bus2.default.$emit('vue-form.invalid.server',response);vm.sending=false;status.reset();status.danger(response.body);});return true;}};};var _bus=__webpack_require__(4);var _bus2=_interopRequireDefault(_bus);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}function getData(method,data){return['head','get','delete'].indexOf(method)>-1?{params:data}:data;}

/***/ },
/* 78 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _bus=__webpack_require__(4);var _bus2=_interopRequireDefault(_bus);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}module.exports=function(vm){return{submit:function submit(e){if(e)e.preventDefault();var data=vm.formData();vm.reinitForm();_bus2.default.$emit('vue-form.sent',data);vm.getStatusBar().success(vm.opts.texts.sent);return true;}};};

/***/ },
/* 79 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(vm){return{submit:function submit(e){if(vm.sending){if(e)e.preventDefault();return false;}vm.sending=true;vm.$el.submit();return true;}};};

/***/ },
/* 80 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';module.exports=function(){var options={labelWidth:3,layout:'',showErrorsInStatusBar:false,sendOnlyDirtyFields:false,successTimeout:4000,additionalPayload:{},customRules:{},fileOptions:{},dateOptions:{},select2Options:{},tinymceOptions:{},beforeSubmit:function beforeSubmit(){return true;},texts:{sending:'Sending Form...',sent:'Form was successfully sent',singleError:'an error was found:',errors:'{0} errors were found:',selectAll:'Select All',unselectAll:'Unselect All'}};options.messages=__webpack_require__(96)();return options;};

/***/ },
/* 81 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var _this=this;var toggler='';var items=[];if(this.multiple){toggler=h("span",{"class":"pull-right btn btn-link",on:{"click":"toggle()"}},[this.toggleText]);}this.items.map(function(item){if(_this.passesFilter(item))items.push(h("div",{"class":"form-check"},[h("label",{"class":"form-check-label"},[h("input",{"class":"form-check-input",attrs:{disabled:_this.disabled,name:_this.name+_this.arraySymbol,type:_this.type,value:item.id,checked:_this.isChecked(item.id)},on:{"change":_this.updateValue.bind(_this,item.id)}},[]),h("span",{"class":"form-check-label-text"},[item.text])])]));});return h("div",{"class":"VF-Buttons__wrapper"},[toggler,items]);};

/***/ },
/* 82 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var hiddenInput='';if(this.inForm()&&this.getForm().server){hiddenInput=h("input",{attrs:{name:this.name,type:"hidden",value:"0"}},[]);}return h("div",null,[hiddenInput,h("input",{attrs:{type:"checkbox",name:this.name,value:"1",checked:this.checked,disabled:this.disabled},on:{"change":this.updateValue.bind(this)}},[])]);};

/***/ },
/* 83 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var hiddenInput='';if(this.inForm()&&this.getForm().server){hiddenInput=h("input",{attrs:{type:"hidden",name:this.name,value:this.serverFormat}},[]);}return h("div",null,[h("input",{"class":"VF-Field--Date__datepicker form-control",attrs:{name:this.name,placeholder:this.placeholder,value:this.formattedDate,type:"text"},on:{"change":this.updateValue.bind(this)}},[]),hiddenInput]);};

/***/ },
/* 84 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var hiddenInput='';if(this.inForm()&&this.getForm().server){hiddenInput=h('input',{attrs:{type:'hidden',name:this.name,value:this.serverFormat}},[]);}return h('div',{'class':'date-wrapper VF-Field--Date__datepicker'},[h('i',{'class':'glyphicon glyphicon-calendar'},[]),h('span',{'class':'VF-Field--Date__date'},[this.formattedDate]),h('b',{'class':'caret'},[]),hiddenInput]);};

/***/ },
/* 85 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var classes='';if(this.isTimepicker)classes+=' VF-Field--Date__timepicker';classes+=this.type=='input'?' date__input':' date__span';return h('div',{'class':'VF-Field--Date__date'+classes},[_dates2.default[this.type].apply(this,[h])]);};var _dates=__webpack_require__(86);var _dates2=_interopRequireDefault(_dates);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}

/***/ },
/* 86 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _dateInput=__webpack_require__(83);var _dateInput2=_interopRequireDefault(_dateInput);var _dateSpan=__webpack_require__(84);var _dateSpan2=_interopRequireDefault(_dateSpan);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}exports.default={input:_dateInput2.default,span:_dateSpan2.default};

/***/ },
/* 87 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var label='';var feedback='';var error='';var form=this.getForm();if(this.hasLabel){label=h('label',{'class':'col-form-label VF-Field__label control-label '+this.getForm().labelClass,attrs:{'for':this.name}},[this.label]);}if(this.validationError){error=h('span',{'class':'VF-ValidationError help-block'},[this.validationError]);}if(this.hasFeedback){h('span',{'class':"glyphicon glyphicon-"+this.feedbackIcon+"form-control-feedback",attrs:{'aria-hidden':'true'}},[]);}return h('div',{directives:[{name:'show',value:this.shouldShow}],attrs:{id:'Field--'+this.name},'class':'VF VF-Field form-group row '+this.fieldClasses},[label,h('div',{'class': true?form.fieldClass:''},[this.$slots.before,form.templates[this.fieldType].apply(this,[h]),feedback,error,this.$slots.after])]);};var _merge=__webpack_require__(0);var _merge2=_interopRequireDefault(_merge);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}

/***/ },
/* 88 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _input=__webpack_require__(91);var _input2=_interopRequireDefault(_input);var _file=__webpack_require__(89);var _file2=_interopRequireDefault(_file);var _select=__webpack_require__(92);var _select2=_interopRequireDefault(_select);var _textarea=__webpack_require__(95);var _textarea2=_interopRequireDefault(_textarea);var _date=__webpack_require__(85);var _date2=_interopRequireDefault(_date);var _checkbox=__webpack_require__(82);var _checkbox2=_interopRequireDefault(_checkbox);var _buttonsList=__webpack_require__(81);var _buttonsList2=_interopRequireDefault(_buttonsList);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}exports.default={text:_input2.default,password:_input2.default,email:_input2.default,number:_input2.default,select:_select2.default,file:_file2.default,textarea:_textarea2.default,date:_date2.default,checkbox:_checkbox2.default,buttons:_buttonsList2.default};

/***/ },
/* 89 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var value='';if(this.curValue)value=h("span",{"class":"VF-Field__file_uploaded glyphicon glyphicon-ok",attrs:{title:this.curValue}},[]);return h("span",{"class":"VF-Field__file-upload"},[h("span",{"class":"glyphicon glyphicon-upload VF-Field__file-upload-icon"},[]),h("input",{attrs:{disabled:this.disabled,type:"file",name:this.name},"class":"form-control-file"},[]),value]);};

/***/ },
/* 90 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var hidden=[];if(!this.ajax&&!this.client){this.additionalValues.map(function(value){hidden.push(h("input",{attrs:{type:"hidden",name:value.name,value:value.value}},[]));});};return h("form",{attrs:{action:this.action,method:this.method,novalidate:true,enctype:"multipart/form-data"},"class":this.opts.layout,on:{"submit":this.submit.bind(this)},slot:"slot"},[hidden,this.$slots.default]);};

/***/ },
/* 91 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){return h("input",{attrs:{type:this.fieldType,name:this.name,value:this.curValue,placeholder:this.placeholder,disabled:this.disabled},on:{"change":this.updateValue.bind(this),"keyup":this.updateValue.bind(this)},"class":"form-control"},[]);};

/***/ },
/* 92 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var _this=this;var placeholder='';var items=[];if(!this.noDefault&&!this.multiple){placeholder=h("option",{attrs:{value:""}},[this.placeholder]);}if(!this.select2||this.ajaxUrl||this.html||this.filterBy){items=this.items.filter(function(item){return!_this.filterValue||item[_this.filterBy]==_this.filterValue;});items=items.map(function(item){return h("option",{attrs:{value:item.id}},[item.text]);});}return h("select",{attrs:{name:this.name+this.arraySymbol,disabled:this.disabled,multiple:this.multiple},on:{"change":this.updateValue.bind(this)},"class":"form-control"},[placeholder,items]);};

/***/ },
/* 93 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var _this=this;if(!this.hasMessage)return'';var message='<p>'+this.Message+'</p>';var errors=[];this.showableErrors.map(function(error){return errors.push(_this.getErrorMessage(error));});errors=errors.map(function(error){return'<li>'+error+'</li>';});var content=this.hasErrors?'<p>'+this.errorsCount+'</p><ul>'+errors.join('')+'</ul>':message;return h('div',{'class':'StatusBar alert alert-'+this.Status,domProps:{'innerHTML':content}},[]);};

/***/ },
/* 94 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){var disabled=this.disabled?'disabled':'';return h('button',{attrs:{type:'submit'},'class':"VF-Submit__button btn btn-primary pull-right "+disabled},[this.text]);};

/***/ },
/* 95 */
/***/ function(module, exports) {

"use strict";
"use strict";Object.defineProperty(exports,"__esModule",{value:true});exports.default=function(h){return h("textarea",{attrs:{name:this.name,id:"textarea_"+this.name,disabled:this.disabled,placeholder:this.placeholder},"class":"form-control",on:{"change":this.updateValue.bind(this),"keyup":this.updateValue.bind(this)}},[]);};

/***/ },
/* 96 */
/***/ function(module, exports) {

"use strict";
'use strict';module.exports=function(){return{required:'The :field field is required',number:'The :field field must be a number',integer:'The :field field must be an integer',digits:'The :field field must have digits only',email:'The :field field must be a valid email address',date:'The :field field must be a valid date according to the :format format',daterange:'The :field field must be a valid date range',between:{string:'The field :field must contain between {0} and {1} characters',number:'The field :field must be a number between {0} and {1}',date:'The field :field must be a date between {0} and {1}'},min:{string:'The :field field must contain at least {0} characters',number:'The :field field must be equal to or greater than {0}',date:'The field :field must be a date equal to or greater than {0}'},max:{string:'The field :field must contain no more than {0} characters',number:'The field :field must be equal to or smaller than {0}',date:'The field :field must be a date equal to or smaller than {0}'},remote:'Remote Error',requiredIf:'The field :field is required',requiredAndShownIf:'The field :field is required',url:'Please enter a valid URL',greaterThan:'The field :field must be greater than :relatedField',smallerThan:'The field :field must be smaller than :relatedField'};};

/***/ },
/* 97 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that){var value=that.Rules.number||that.Rules.integer||_typeof(that.Rules.between[0])=='object'?// moment objects
that.curValue:that.curValue.length;if(that.Rules.number||that.Rules.integer)value=parseFloat(value);return value>=that.Rules.between[0]&&value<=that.Rules.between[1];};

/***/ },
/* 98 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(field){if(!field.curValue)return true;return field.curValue.isValid();};

/***/ },
/* 99 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(field){var value=field.momentizeValue(field.curValue);return value.start.isValid()&&value.end.isValid()&&value.end>=value.start;};

/***/ },
/* 100 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(that){return /^[0-9]*$/.test(that.curValue.trim());};

/***/ },
/* 101 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(that){var re=/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return!that.curValue.trim()||re.test(that.curValue);};

/***/ },
/* 102 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that){if(!that.curValue)return true;var otherField=that.getField(that.Rules.greaterThan);if(!otherField||!otherField.curValue)return true;var value1=!!isNaN(that.curValue)||_typeof(that.curValue)=='object'?that.curValue:parseFloat(that.curValue);var value2=!!isNaN(otherField.curValue)||_typeof(otherField.curValue)=='object'?otherField.curValue:parseFloat(otherField.curValue);return value1>value2;};

/***/ },
/* 103 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(that){return!isNaN(that.curValue)&&that.curValue%1===0;};

/***/ },
/* 104 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that){var value=that.Rules.number||that.Rules.integer||_typeof(that.Rules.max)=='object'?// moment object
that.curValue:that.curValue.length;if(that.Rules.number||that.Rules.integer)value=parseFloat(value);return value<=that.Rules.max;};

/***/ },
/* 105 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that){var value=that.Rules.number||that.Rules.integer||_typeof(that.Rules.min)=='object'?// moment object
that.curValue:that.curValue.length;if(that.Rules.number||that.Rules.integer)value=parseFloat(value);return value>=that.Rules.min;};

/***/ },
/* 106 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var isNumeric=__webpack_require__(15);module.exports=function(that){return isNumeric(that.curValue);};

/***/ },
/* 107 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var requiredIfBase=__webpack_require__(17);var hasValue=__webpack_require__(8);module.exports=function(that){var required=requiredIfBase(that,'requiredAndShownIf');that.shouldShow=required;return!required||hasValue(that)||that.fieldType=='checkbox';};

/***/ },
/* 108 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var requiredIfBase=__webpack_require__(17);var hasValue=__webpack_require__(8);module.exports=function(that){var required=requiredIfBase(that,'requiredIf');console.log(required);return!required||hasValue(that)||that.fieldType=='checkbox';};

/***/ },
/* 109 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var hasValue=__webpack_require__(8);module.exports=function(that){if(!that.Rules.required){return true;}return hasValue(that);};

/***/ },
/* 110 */
/***/ function(module, exports) {

"use strict";
'use strict';var _typeof=typeof Symbol==="function"&&typeof Symbol.iterator==="symbol"?function(obj){return typeof obj;}:function(obj){return obj&&typeof Symbol==="function"&&obj.constructor===Symbol&&obj!==Symbol.prototype?"symbol":typeof obj;};module.exports=function(that){if(!that.curValue)return true;var otherField=that.getField(that.Rules.smallerThan);if(!otherField||!otherField.curValue)return true;var value1=!!isNaN(that.curValue)||_typeof(that.curValue)=='object'?that.curValue:parseFloat(that.curValue);var value2=!!isNaN(otherField.curValue)||_typeof(otherField.curValue)=='object'?otherField.curValue:parseFloat(otherField.curValue);return value1<value2;};

/***/ },
/* 111 */
/***/ function(module, exports) {

"use strict";
"use strict";module.exports=function(that){return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(that.curValue.trim());};

/***/ },
/* 112 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});exports.default={name:'FlexyBlocks',props:['text','href'],computed:{chars:function chars(){var text=this.text.split('');var chars=[];for(var i=0;i<text.length;i++){chars[i]={char:text[i],css:'char-'+(text[i]===' '?'space':text[i])};}return chars;}}};

/***/ },
/* 113 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});//
//
//
//
//
//
//
//
//
//
//
//
//
exports.default={name:'VueGridCell',props:['type','name','value'],computed:{cellClass:function cellClass(){return'grid-cell-type-'+this.type;}}};

/***/ },
/* 114 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});var _vuePagination=__webpack_require__(11);var _vuePagination2=_interopRequireDefault(_vuePagination);var _vueGridCell=__webpack_require__(120);var _vueGridCell2=_interopRequireDefault(_vueGridCell);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
exports.default={name:'VueGrid',components:{VueGridCell:_vueGridCell2.default,VuePagination:_vuePagination2.default},props:{'title':String,'name':String,'fields':[Object,Array],'data':Array,'info':Object,'order':{type:String,default:''},'find':{type:[String,Object],default:''}},computed:{/**
     * Bepaal het type grid: table, ordered of tree
     */gridType:function gridType(){var type='table';if(typeof this.fields.order!=='undefined')type='ordered';if(typeof this.fields.self_parent!=='undefined')type='tree';return type;},gridTypeClass:function gridTypeClass(){return'grid-type-'+this.gridType;},/**
     * Prepare gridData (schema)
     */gridData:function gridData(){var data=this.data;for(var i=0;i<data.length;i++){var row=data[i];var id=row['id'];for(var field in row){var schema={'type':'string','form-type':'text','readonly':false};if(this.fields[field])schema=this.fields[field].schema;data[i][field]={'type':schema['form-type'],'value':row[field]};if(schema.type==='number'&&schema['form-type']==='select'){var jsonValue=JSON.parse(row[field].value);data[i][field]={'type':schema['form-type'],'value':Object.values(jsonValue)[0],'id':Object.keys(jsonValue)[0]};}data[i][field].name=field;}data[i]=row;}return data;},/**
     * Test if grid needs pagination
     */needsPagination:function needsPagination(){return typeof this.info.num_pages!=='undefined'&&this.info.num_pages>1;}},methods:{headerClass:function headerClass(field){return'grid-header-type-'+field.schema['form-type'];},/**
     * Create url, used for all links (pagination, edit, sort etc..)
     */createdUrl:function createdUrl(parts){var defaults={order:this.order,find:this.find,offset:this.info.offset};parts=_.extend(defaults,parts);return location.pathname+'?options={"offset":"'+parts.offset+'","order":"'+parts.order+'","find":"'+parts.find+'"}';},editUrl:function editUrl(id){return'admin/show/form/'+this.name+'/'+id;}}};

/***/ },
/* 115 */
/***/ function(module, exports) {

"use strict";
'use strict';Object.defineProperty(exports,"__esModule",{value:true});//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
exports.default={name:'VuePagination',props:{'total':Number,// total number of rows
'pages':Number,// pages number of pages
'current':Number,// current page
'limit':Number,// items per page
'url':String,// template for building url, where {{offset}} will be replaced with the offset
'buttons':{// Number of page-buttons used for pagination
type:Number,default:5}},methods:{/**
      Calculates number of buttons needed, returns it as an array
     */pagesButtons:function pagesButtons(){if(this.buttons>=this.pages)return this.pages;var min=this.current-Math.floor(this.buttons/2);var max=this.current+Math.floor(this.buttons/2);while(min<=0){min++;max++;}while(max>=this.pages){min--;max--;}var numberButtons=[];for(var i=min;i<=max;i++){numberButtons.push(i);}return numberButtons;},/**
     * Creates the URL for each button
     */pageUrl:function pageUrl(page){return this.url.replace('##',(page-1)*this.limit);}}};

/***/ },
/* 116 */
/***/ function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(3)();
// imports


// module
exports.push([module.i, "\n.grid .card-block {padding:0;\n}\n.grid .card-footer {padding:.25rem .25rem 0;\n}\n.grid th {overflow:hidden;text-overflow:ellipsis;\n}\n.grid th a {text-decoration:none;\n}\n.grid th span {white-space:nowrap;text-transform:uppercase;\n}\n.grid th > span.fa {position:relative;float:right;margin-top:.25rem;\n}\n.grid th.grid-header-type-primary {width:10rem;max-width:10rem;\n}\n.grid.grid-type-tree th.grid-header-type-primary {width:10rem;max-width:10rem;\n}\n.grid.grid-type-table th.grid-header-type-primary {width:7.75rem;max-width:7.75rem;\n}\n", ""]);

// exports


/***/ },
/* 117 */
/***/ function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(3)();
// imports


// module
exports.push([module.i, "\n.grid td {overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:250px;\n}\n.grid td.grid-cell-type-checkbox {text-align:center;\n}\n", ""]);

// exports


/***/ },
/* 118 */
/***/ function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(3)();
// imports


// module
exports.push([module.i, "\n.pagination-container {width:100%;\n}\n.pagination {margin:0;\n}\n.pagination-info {\n  margin-top:.5rem;\n  float:right;\n}\n", ""]);

// exports


/***/ },
/* 119 */
/***/ function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__(3)();
// imports


// module
exports.push([module.i, "\n.flexy-block {text-transform:uppercase;margin-right:1px;\n}\n.flexy-block.text-lowercase {text-transform:lowercase;\n}\n.flexy-block.char-space {visibility:hidden;\n}\n", ""]);

// exports


/***/ },
/* 120 */
/***/ function(module, exports, __webpack_require__) {

var __vue_exports__, __vue_options__

/* styles */
__webpack_require__(126)

/* script */
__vue_exports__ = __webpack_require__(113)

/* template */
var __vue_template__ = __webpack_require__(122)
__vue_options__ = __vue_exports__ = __vue_exports__ || {}
if (
  typeof __vue_exports__.default === "object" ||
  typeof __vue_exports__.default === "function"
) {
if (Object.keys(__vue_exports__).some(function (key) { return key !== "default" && key !== "__esModule" })) {console.error("named exports are not supported in *.vue files.")}
__vue_options__ = __vue_exports__ = __vue_exports__.default
}
if (typeof __vue_options__ === "function") {
  __vue_options__ = __vue_options__.options
}
__vue_options__.__file = "/Users/jan/Sites/FlexyAdmin/FlexyAdmin/sys/flexyadmin/assets/js/vue-components/vue-grid-cell.vue"
__vue_options__.render = __vue_template__.render
__vue_options__.staticRenderFns = __vue_template__.staticRenderFns

/* hot reload */
if (false) {(function () {
  var hotAPI = require("vue-hot-reload-api")
  hotAPI.install(require("vue"), false)
  if (!hotAPI.compatible) return
  module.hot.accept()
  if (!module.hot.data) {
    hotAPI.createRecord("data-v-4bbb9245", __vue_options__)
  } else {
    hotAPI.reload("data-v-4bbb9245", __vue_options__)
  }
})()}
if (__vue_options__.functional) {console.error("[vue-loader] vue-grid-cell.vue: functional components are not supported and should be defined in plain js files using render functions.")}

module.exports = __vue_exports__


/***/ },
/* 121 */
/***/ function(module, exports, __webpack_require__) {

module.exports={render:function (){with(this) {
  return _h('div', {
    staticClass: "card grid",
    class: gridTypeClass
  }, [_h('h1', {
    staticClass: "card-header"
  }, [_s(title)]), " ", _h('div', {
    staticClass: "card-block table-responsive"
  }, [_h('table', {
    staticClass: "table table-bordered table-hover table-sm"
  }, [_h('thead', [_h('tr', [_l((fields), function(field, key) {
    return [(field.schema['form-type'] === 'primary') ? _h('th', {
      staticClass: "text-primary",
      class: headerClass(field)
    }, [_h('a', {
      staticClass: "btn btn-sm btn-success",
      attrs: {
        "href": editUrl(-1)
      }
    }, [_m(0, true)]), " ", _m(1, true), " ", _m(2, true)]) : _e(), " ", (field.schema['form-type'] !== 'hidden' && field.schema['form-type'] !== 'primary') ? _h('th', {
      staticClass: "text-primary",
      class: headerClass(field)
    }, [_h('a', {
      attrs: {
        "href": createdUrl({
          'order': (key == order ? '_' + key : key)
        })
      }
    }, [_h('span', [_s(field.name)]), " ", (order == key) ? _h('span', {
      staticClass: "fa fa-caret-up"
    }) : _e(), " ", (order == '_' + key) ? _h('span', {
      staticClass: "fa fa-caret-down"
    }) : _e()])]) : _e()]
  })])]), " ", _h('tbody', [_l((gridData), function(row) {
    return _h('tr', {
      attrs: {
        "data-id": row.id.value
      }
    }, [_l((row), function(cell) {
      return [(cell.type == 'primary') ? _h('td', {
        staticClass: "action"
      }, [_h('a', {
        staticClass: "btn btn-sm btn-success",
        attrs: {
          "href": editUrl(cell.value)
        }
      }, [_m(3, true)]), " ", _m(4, true), " ", _m(5, true), " ", (gridType !== 'table') ? _h('div', {
        staticClass: "btn btn-sm btn-warning action-move"
      }, [_m(6, true)]) : _e()]) : _h('vue-grid-cell', {
        attrs: {
          "type": cell.type,
          "name": cell.name
        },
        domProps: {
          "value": cell.value
        }
      }), " "]
    })])
  })])])]), " ", (needsPagination) ? _h('div', {
    staticClass: "card-footer text-muted"
  }, [_h('vue-pagination', {
    attrs: {
      "total": info.total_rows,
      "pages": info.num_pages,
      "current": info.page + 1,
      "limit": info.limit,
      "url": createdUrl({
        'offset': '##'
      })
    }
  })]) : _e()])
}},staticRenderFns: [function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-plus"
  })
}},function (){with(this) {
  return _h('div', {
    staticClass: "btn btn-sm btn-info action-select"
  }, [_h('span', {
    staticClass: "fa fa-square-o"
  }), _h('span', {
    staticClass: "fa fa-check-square-o"
  })])
}},function (){with(this) {
  return _h('div', {
    staticClass: "btn btn-sm btn-danger action-delete"
  }, [_h('span', {
    staticClass: "fa fa-remove"
  })])
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-pencil"
  })
}},function (){with(this) {
  return _h('div', {
    staticClass: "btn btn-sm btn-info action-select"
  }, [_h('span', {
    staticClass: "fa fa-square-o"
  }), _h('span', {
    staticClass: "fa fa-check-square-o"
  })])
}},function (){with(this) {
  return _h('div', {
    staticClass: "btn btn-sm btn-danger action-delete"
  }, [_h('span', {
    staticClass: "fa fa-remove"
  })])
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-bars"
  })
}}]}
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-06b0788c", module.exports)
  }
}

/***/ },
/* 122 */
/***/ function(module, exports, __webpack_require__) {

module.exports={render:function (){with(this) {
  return (type !== 'hidden') ? _h('td', {
    class: cellClass,
    attrs: {
      "type": type,
      "value": "value"
    }
  }, [(type == 'text') ? [_s(value)] : _e(), " ", (type == 'wysiwyg') ? [_s(value)] : _e(), " ", (type == 'media') ? [_s(value)] : _e(), " ", (type == 'checkbox') ? [(value) ? _h('span', {
    staticClass: "fa fa-check text-success",
    domProps: {
      "value": value
    }
  }) : _h('span', {
    staticClass: "fa fa-minus text-warning",
    domProps: {
      "value": value
    }
  }), " "] : _e(), " ", (type == 'select') ? [_s(value)] : _e()]) : _e()
}},staticRenderFns: []}
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-4bbb9245", module.exports)
  }
}

/***/ },
/* 123 */
/***/ function(module, exports, __webpack_require__) {

module.exports={render:function (){with(this) {
  return _h('div', {
    staticClass: "pagination-container"
  }, [_h('ul', {
    staticClass: "pagination"
  }, [(current > 1 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(1)
    }
  }, [_m(0)])]) : _e(), " ", (current > 10 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(current - 10)
    }
  }, [_m(1), _m(2)])]) : _e(), " ", (current > 1 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(current - 1)
    }
  }, [_m(3)])]) : _e(), " ", _l((pagesButtons()), function(page) {
    return _h('li', {
      staticClass: "page-item",
      class: {
        active: (page == current)
      }
    }, [_h('a', {
      staticClass: "page-link",
      attrs: {
        "href": pageUrl(page)
      }
    }, [_s(page)])])
  }), " ", (current < pages - 1 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(current + 1)
    }
  }, [_m(4)])]) : _e(), " ", (current < pages - 10 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(current + 10)
    }
  }, [_m(5), _m(6)])]) : _e(), " ", (current < pages - 1 && pages > buttons) ? _h('li', {
    staticClass: "page-item"
  }, [_h('a', {
    staticClass: "page-link",
    attrs: {
      "href": pageUrl(pages - 1)
    }
  }, [_m(7)])]) : _e()]), " ", _h('span', {
    staticClass: "pagination-info text-primary"
  }, [_s(total) + " rijen in " + _s(pages) + " pagina's"])])
}},staticRenderFns: [function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-fast-backward"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-left"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-left"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-left"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-right"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-right"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-chevron-right"
  })
}},function (){with(this) {
  return _h('span', {
    staticClass: "fa fa-fast-forward"
  })
}}]}
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-7d03110e", module.exports)
  }
}

/***/ },
/* 124 */
/***/ function(module, exports, __webpack_require__) {

module.exports={render:function (){with(this) {
  return _h('a', {
    attrs: {
      "href": href
    }
  }, [_m(0)])
}},staticRenderFns: [function (){with(this) {
  return _l((chars), function(char) {
    return _h('span', {
      staticClass: "flexy-block btn btn-secondary",
      class: char.css
    }, [_s(char.char)])
  })
}}]}
if (false) {
  module.hot.accept()
  if (module.hot.data) {
     require("vue-hot-reload-api").rerender("data-v-7db827ae", module.exports)
  }
}

/***/ },
/* 125 */
/***/ function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(116);
if(typeof content === 'string') content = [[module.i, content, '']];
// add the styles to the DOM
var update = __webpack_require__(6)(content, {});
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-06b0788c!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-grid.vue", function() {
			var newContent = require("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-06b0788c!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-grid.vue");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ },
/* 126 */
/***/ function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(117);
if(typeof content === 'string') content = [[module.i, content, '']];
// add the styles to the DOM
var update = __webpack_require__(6)(content, {});
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-4bbb9245!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-grid-cell.vue", function() {
			var newContent = require("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-4bbb9245!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-grid-cell.vue");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ },
/* 127 */
/***/ function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(118);
if(typeof content === 'string') content = [[module.i, content, '']];
// add the styles to the DOM
var update = __webpack_require__(6)(content, {});
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-7d03110e!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-pagination.vue", function() {
			var newContent = require("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-7d03110e!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./vue-pagination.vue");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ },
/* 128 */
/***/ function(module, exports, __webpack_require__) {

// style-loader: Adds some css to the DOM by adding a <style> tag

// load the styles
var content = __webpack_require__(119);
if(typeof content === 'string') content = [[module.i, content, '']];
// add the styles to the DOM
var update = __webpack_require__(6)(content, {});
if(content.locals) module.exports = content.locals;
// Hot Module Replacement
if(false) {
	// When the styles change, update the <style> tags
	if(!content.locals) {
		module.hot.accept("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-7db827ae!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./flexyblocks.vue", function() {
			var newContent = require("!!./../../../../node_modules/css-loader/index.js!./../../../../node_modules/vue-loader/lib/style-rewriter.js?id=data-v-7db827ae!./../../../../node_modules/vue-loader/lib/selector.js?type=styles&index=0!./flexyblocks.vue");
			if(typeof newContent === 'string') newContent = [[module.id, newContent, '']];
			update(newContent);
		});
	}
	// When the module is disposed, remove the <style> tags
	module.hot.dispose(function() { update(); });
}

/***/ },
/* 129 */
/***/ function(module, exports, __webpack_require__) {

"use strict";
'use strict';var _vue=__webpack_require__(10);var _vue2=_interopRequireDefault(_vue);var _vueForm=__webpack_require__(21);var _vueForm2=_interopRequireDefault(_vueForm);var _flexyblocks=__webpack_require__(22);var _flexyblocks2=_interopRequireDefault(_flexyblocks);var _vuePagination=__webpack_require__(11);var _vuePagination2=_interopRequireDefault(_vuePagination);var _vueGrid=__webpack_require__(23);var _vueGrid2=_interopRequireDefault(_vueGrid);function _interopRequireDefault(obj){return obj&&obj.__esModule?obj:{default:obj};}/**
 * Bootstrapping FlexyAdmin:
 * - Create Vue Instance
 * 
 * @author: Jan den Besten
 */// var _ = require('lodash');
var _=__webpack_require__(20);// var css = require("!css!sass!../scss/flexyadmin.scss");
// // HACK TODO: Require does't work as expected, so include it by hand
// var head = document.head || document.getElementsByTagName('head')[0];
// var style = document.createElement('style');
// style.type = 'text/css';
// style.appendChild(document.createTextNode(css));
// head.appendChild(style);
// Every component logs its name and props
// Vue.mixin({
//   created: function () {
//     if (this.$options._componentTag) {
//       console.log(this.$options._componentTag, this.$options.propsData);
//     }
//     else {
//       console.log('Some Vue Component/Instance ready');
//     }
//   },
// });
_vue2.default.use(_vueForm2.default,{layout:'form-horizontal'});var vm=new _vue2.default({el:'#main',components:{FlexyBlocks:_flexyblocks2.default,VuePagination:_vuePagination2.default,VueGrid:_vueGrid2.default}});

/***/ }
/******/ ]);