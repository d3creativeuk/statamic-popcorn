import{t as e}from"./rolldown-runtime-DK3Fl9T5.js";var t=Object.create,n=Object.defineProperty,r=Object.getOwnPropertyDescriptor,i=Object.getOwnPropertyNames,a=Object.getPrototypeOf,o=Object.prototype.hasOwnProperty,s=(e,t)=>function(){return t||(0,e[i(e)[0]])((t={exports:{}}).exports,t),t.exports},c=(e,t,a,s)=>{if(t&&typeof t==`object`||typeof t==`function`)for(let c of i(t))!o.call(e,c)&&c!==a&&n(e,c,{get:()=>t[c],enumerable:!(s=r(t,c))||s.enumerable});return e},l=(e,r,i)=>(i=e==null?{}:t(a(e)),c(r||!e||!e.__esModule?n(i,`default`,{value:e,enumerable:!0}):i,e)),u=s({"node_modules/global/window.js"(e,t){t.exports=typeof window<`u`?window:typeof global<`u`?global:typeof self<`u`?self:{}}}),d=s({"node_modules/is-function/index.js"(e,t){t.exports=r;var n=Object.prototype.toString;function r(e){if(!e)return!1;var t=n.call(e);return t===`[object Function]`||typeof e==`function`&&t!==`[object RegExp]`||typeof window<`u`&&(e===window.setTimeout||e===window.alert||e===window.confirm||e===window.prompt)}}}),f=s({"node_modules/parse-headers/parse-headers.js"(e,t){var n=function(e){return e.replace(/^\s+|\s+$/g,``)},r=function(e){return Object.prototype.toString.call(e)===`[object Array]`};t.exports=function(e){if(!e)return{};for(var t={},i=n(e).split(`
`),a=0;a<i.length;a++){var o=i[a],s=o.indexOf(`:`),c=n(o.slice(0,s)).toLowerCase(),l=n(o.slice(s+1));t[c]===void 0?t[c]=l:r(t[c])?t[c].push(l):t[c]=[t[c],l]}return t}}}),p=s({"node_modules/xtend/immutable.js"(e,t){t.exports=r;var n=Object.prototype.hasOwnProperty;function r(){for(var e={},t=0;t<arguments.length;t++){var r=arguments[t];for(var i in r)n.call(r,i)&&(e[i]=r[i])}return e}}}),m=s({"node_modules/xhr/index.js"(e,t){var n=u(),r=d(),i=f(),a=p();t.exports=l,t.exports.default=l,l.XMLHttpRequest=n.XMLHttpRequest||h,l.XDomainRequest=`withCredentials`in new l.XMLHttpRequest?l.XMLHttpRequest:n.XDomainRequest,o([`get`,`put`,`post`,`patch`,`head`,`delete`],function(e){l[e===`delete`?`del`:e]=function(t,n,r){return n=c(t,n,r),n.method=e.toUpperCase(),m(n)}});function o(e,t){for(var n=0;n<e.length;n++)t(e[n])}function s(e){for(var t in e)if(e.hasOwnProperty(t))return!1;return!0}function c(e,t,n){var i=e;return r(t)?(n=t,typeof e==`string`&&(i={uri:e})):i=a(t,{uri:e}),i.callback=n,i}function l(e,t,n){return t=c(e,t,n),m(t)}function m(e){if(e.callback===void 0)throw Error(`callback argument missing`);var t=!1,n=function(n,r,i){t||(t=!0,e.callback(n,r,i))};function r(){u.readyState===4&&setTimeout(c,0)}function a(){var e=void 0;if(e=u.response?u.response:u.responseText||ee(u),_)try{e=JSON.parse(e)}catch{}return e}function o(e){return clearTimeout(v),e instanceof Error||(e=Error(``+(e||`Unknown XMLHttpRequest Error`))),e.statusCode=0,n(e,ne)}function c(){if(!f){var t;clearTimeout(v),t=e.useXDR&&u.status===void 0?200:u.status===1223?204:u.status;var r=ne,o=null;return t===0?o=Error(`Internal XMLHttpRequest Error`):(r={body:a(),statusCode:t,method:m,headers:{},url:p,rawRequest:u},u.getAllResponseHeaders&&(r.headers=i(u.getAllResponseHeaders()))),n(o,r,r.body)}}var u=e.xhr||null;u||=e.cors||e.useXDR?new l.XDomainRequest:new l.XMLHttpRequest;var d,f,p=u.url=e.uri||e.url,m=u.method=e.method||`GET`,h=e.body||e.data,g=u.headers=e.headers||{},te=!!e.sync,_=!1,v,ne={body:void 0,headers:{},statusCode:0,method:m,url:p,rawRequest:u};if(`json`in e&&e.json!==!1&&(_=!0,g.accept||g.Accept||(g.Accept=`application/json`),m!==`GET`&&m!==`HEAD`&&(g[`content-type`]||g[`Content-Type`]||(g[`Content-Type`]=`application/json`),h=JSON.stringify(e.json===!0?h:e.json))),u.onreadystatechange=r,u.onload=c,u.onerror=o,u.onprogress=function(){},u.onabort=function(){f=!0},u.ontimeout=o,u.open(m,p,!te,e.username,e.password),te||(u.withCredentials=!!e.withCredentials),!te&&e.timeout>0&&(v=setTimeout(function(){if(!f){f=!0,u.abort(`timeout`);var e=Error(`XMLHttpRequest timeout`);e.code=`ETIMEDOUT`,o(e)}},e.timeout)),u.setRequestHeader)for(d in g)g.hasOwnProperty(d)&&u.setRequestHeader(d,g[d]);else if(e.headers&&!s(e.headers))throw Error(`Headers cannot be set on an XDomainRequest object`);return`responseType`in e&&(u.responseType=e.responseType),`beforeSend`in e&&typeof e.beforeSend==`function`&&e.beforeSend(u),u.send(h||null),u}function ee(e){try{if(e.responseType===`document`)return e.responseXML;var t=e.responseXML&&e.responseXML.documentElement.nodeName===`parsererror`;if(e.responseType===``&&!t)return e.responseXML}catch{}return null}function h(){}}});function ee(e,t,...n){if(!e)throw TypeError(h(t,n))}function h(e,t){let n=0;return e.replace(/%[os]/gu,()=>g(t[n++]))}function g(e){return typeof e!=`object`||!e?String(e):Object.prototype.toString.call(e)}function te(e){try{let t=e instanceof Error?e:Error(g(e));if(typeof dispatchEvent==`function`&&typeof ErrorEvent==`function`)dispatchEvent(new ErrorEvent(`error`,{error:t,message:t.message}));else if(typeof process<`u`&&typeof process.emit==`function`){process.emit(`uncaughtException`,t);return}console.error(t)}catch{}}var _=typeof window<`u`?window:typeof self<`u`?self:typeof global<`u`?global:typeof globalThis<`u`?globalThis:void 0,v=class{constructor(e,t){this.code=e,this.message=t}warn(...e){try{let t=(Error().stack??``).replace(/^(?:.+?\n){2}/gu,`
`);console.warn(this.message,...e,t)}catch{}}},ne=new v(`W01`,`Unable to initialize event under dispatching.`),re=new v(`W02`,`Assigning any falsy value to 'cancelBubble' property has no effect.`),ie=new v(`W03`,`Assigning any truthy value to 'returnValue' property has no effect.`),ae=new v(`W04`,`Unable to preventDefault on non-cancelable events.`),oe=new v(`W05`,`Unable to preventDefault inside passive event listener invocation.`),se=new v(`W06`,`An event listener wasn't added because it has been added already: %o, %o`),ce=new v(`W07`,`The %o option value was abandoned because the event listener wasn't added as duplicated.`),le=new v(`W08`,`The 'callback' argument must be a function or an object that has 'handleEvent' method: %o`);new v(`W09`,`Event attribute handler must be a function: %o`);var y=class{static get NONE(){return ue}static get CAPTURING_PHASE(){return de}static get AT_TARGET(){return fe}static get BUBBLING_PHASE(){return pe}constructor(e,t){Object.defineProperty(this,"isTrusted",{value:!1,enumerable:!0});let n=t??{};me.set(this,{type:String(e),bubbles:!!n.bubbles,cancelable:!!n.cancelable,composed:!!n.composed,target:null,currentTarget:null,stopPropagationFlag:!1,stopImmediatePropagationFlag:!1,canceledFlag:!1,inPassiveListenerFlag:!1,dispatchFlag:!1,timeStamp:Date.now()})}get type(){return b(this).type}get target(){return b(this).target}get srcElement(){return b(this).target}get currentTarget(){return b(this).currentTarget}composedPath(){let e=b(this).currentTarget;return e?[e]:[]}get NONE(){return ue}get CAPTURING_PHASE(){return de}get AT_TARGET(){return fe}get BUBBLING_PHASE(){return pe}get eventPhase(){return b(this).dispatchFlag?2:0}stopPropagation(){b(this).stopPropagationFlag=!0}get cancelBubble(){return b(this).stopPropagationFlag}set cancelBubble(e){e?b(this).stopPropagationFlag=!0:re.warn()}stopImmediatePropagation(){let e=b(this);e.stopPropagationFlag=e.stopImmediatePropagationFlag=!0}get bubbles(){return b(this).bubbles}get cancelable(){return b(this).cancelable}get returnValue(){return!b(this).canceledFlag}set returnValue(e){e?ie.warn():he(b(this))}preventDefault(){he(b(this))}get defaultPrevented(){return b(this).canceledFlag}get composed(){return b(this).composed}get isTrusted(){return!1}get timeStamp(){return b(this).timeStamp}initEvent(e,t=!1,n=!1){let r=b(this);r.dispatchFlag?ne.warn():me.set(this,{...r,type:String(e),bubbles:!!t,cancelable:!!n,target:null,currentTarget:null,stopPropagationFlag:!1,stopImmediatePropagationFlag:!1,canceledFlag:!1})}},ue=0,de=1,fe=2,pe=3,me=new WeakMap;function b(e,t=`this`){let n=me.get(e);return ee(n!=null,`'%s' must be an object that Event constructor created, but got another one: %o`,t,e),n}function he(e){e.inPassiveListenerFlag?oe.warn():e.cancelable?e.canceledFlag=!0:ae.warn()}Object.defineProperty(y,"NONE",{enumerable:!0}),Object.defineProperty(y,"CAPTURING_PHASE",{enumerable:!0}),Object.defineProperty(y,"AT_TARGET",{enumerable:!0}),Object.defineProperty(y,"BUBBLING_PHASE",{enumerable:!0});var ge=Object.getOwnPropertyNames(y.prototype);for(let e=0;e<ge.length;++e)ge[e]!==`constructor`&&Object.defineProperty(y.prototype,ge[e],{enumerable:!0});_!==void 0&&_.Event!==void 0&&Object.setPrototypeOf(y.prototype,_.Event.prototype);function _e(e){return _.DOMException?new _.DOMException(e,`InvalidStateError`):(x??(x=class e extends Error{constructor(t){super(t),Error.captureStackTrace&&Error.captureStackTrace(this,e)}get code(){return 11}get name(){return`InvalidStateError`}},Object.defineProperties(x.prototype,{code:{enumerable:!0},name:{enumerable:!0}}),ye(x),ye(x.prototype)),new x(e))}var x,ve={INDEX_SIZE_ERR:1,DOMSTRING_SIZE_ERR:2,HIERARCHY_REQUEST_ERR:3,WRONG_DOCUMENT_ERR:4,INVALID_CHARACTER_ERR:5,NO_DATA_ALLOWED_ERR:6,NO_MODIFICATION_ALLOWED_ERR:7,NOT_FOUND_ERR:8,NOT_SUPPORTED_ERR:9,INUSE_ATTRIBUTE_ERR:10,INVALID_STATE_ERR:11,SYNTAX_ERR:12,INVALID_MODIFICATION_ERR:13,NAMESPACE_ERR:14,INVALID_ACCESS_ERR:15,VALIDATION_ERR:16,TYPE_MISMATCH_ERR:17,SECURITY_ERR:18,NETWORK_ERR:19,ABORT_ERR:20,URL_MISMATCH_ERR:21,QUOTA_EXCEEDED_ERR:22,TIMEOUT_ERR:23,INVALID_NODE_TYPE_ERR:24,DATA_CLONE_ERR:25};function ye(e){let t=Object.keys(ve);for(let n=0;n<t.length;++n){let r=t[n],i=ve[r];Object.defineProperty(e,r,{get(){return i},configurable:!0,enumerable:!0})}}var S=class extends y{static wrap(e){return new(xe(e))(e)}constructor(e){super(e.type,{bubbles:e.bubbles,cancelable:e.cancelable,composed:e.composed}),e.cancelBubble&&super.stopPropagation(),e.defaultPrevented&&super.preventDefault(),be.set(this,{original:e});let t=Object.keys(e);for(let n=0;n<t.length;++n){let r=t[n];r in this||Object.defineProperty(this,r,Ce(e,r))}}stopPropagation(){super.stopPropagation();let{original:e}=C(this);`stopPropagation`in e&&e.stopPropagation()}get cancelBubble(){return super.cancelBubble}set cancelBubble(e){super.cancelBubble=e;let{original:t}=C(this);`cancelBubble`in t&&(t.cancelBubble=e)}stopImmediatePropagation(){super.stopImmediatePropagation();let{original:e}=C(this);`stopImmediatePropagation`in e&&e.stopImmediatePropagation()}get returnValue(){return super.returnValue}set returnValue(e){super.returnValue=e;let{original:t}=C(this);`returnValue`in t&&(t.returnValue=e)}preventDefault(){super.preventDefault();let{original:e}=C(this);`preventDefault`in e&&e.preventDefault()}get timeStamp(){let{original:e}=C(this);return`timeStamp`in e?e.timeStamp:super.timeStamp}},be=new WeakMap;function C(e){let t=be.get(e);return ee(t!=null,`'this' is expected an Event object, but got`,e),t}var w=new WeakMap;w.set(Object.prototype,S),_!==void 0&&_.Event!==void 0&&w.set(_.Event.prototype,S);function xe(e){let t=Object.getPrototypeOf(e);if(t==null)return S;let n=w.get(t);return n??(n=Se(xe(t),t),w.set(t,n)),n}function Se(e,t){class n extends e{}let r=Object.keys(t);for(let e=0;e<r.length;++e)Object.defineProperty(n.prototype,r[e],Ce(t,r[e]));return n}function Ce(e,t){let n=Object.getOwnPropertyDescriptor(e,t);return{get(){let e=C(this).original,n=e[t];return typeof n==`function`?n.bind(e):n},set(e){let n=C(this).original;n[t]=e},configurable:n.configurable,enumerable:n.enumerable}}function we(e,t,n,r,i,a){return{callback:e,flags:!!t|(n?2:0)|(r?4:0),signal:i,signalListener:a}}function Te(e){e.flags|=8}function Ee(e){return(e.flags&1)==1}function De(e){return(e.flags&2)==2}function Oe(e){return(e.flags&4)==4}function ke(e){return(e.flags&8)==8}function Ae({callback:e},t,n){try{typeof e==`function`?e.call(t,n):typeof e.handleEvent==`function`&&e.handleEvent(n)}catch(e){te(e)}}function je({listeners:e},t,n){for(let r=0;r<e.length;++r)if(e[r].callback===t&&Ee(e[r])===n)return r;return-1}function Me(e,t,n,r,i,a){let o;a&&(o=Ne.bind(null,e,t,n),a.addEventListener(`abort`,o));let s=we(t,n,r,i,a,o);return e.cow?(e.cow=!1,e.listeners=[...e.listeners,s]):e.listeners.push(s),s}function Ne(e,t,n){let r=je(e,t,n);return r!==-1&&Pe(e,r)}function Pe(e,t,n=!1){let r=e.listeners[t];return Te(r),r.signal&&r.signal.removeEventListener(`abort`,r.signalListener),e.cow&&!n?(e.cow=!1,e.listeners=e.listeners.filter((e,n)=>n!==t),!1):(e.listeners.splice(t,1),!0)}function Fe(){return Object.create(null)}function Ie(e,t){return e[t]??(e[t]={attrCallback:void 0,attrListener:void 0,cow:!1,listeners:[]})}var T=class{constructor(){Le.set(this,Fe())}addEventListener(e,t,n){let r=Re(this),{callback:i,capture:a,once:o,passive:s,signal:c,type:l}=ze(e,t,n);if(i==null||c?.aborted)return;let u=Ie(r,l),d=je(u,i,a);d===-1?Me(u,i,a,s,o,c):He(u.listeners[d],s,o,c)}removeEventListener(e,t,n){let r=Re(this),{callback:i,capture:a,type:o}=Be(e,t,n),s=r[o];i!=null&&s&&Ne(s,i,a)}dispatchEvent(e){let t=Re(this)[String(e.type)];if(t==null)return!0;let n=e instanceof y?e:S.wrap(e),r=b(n,`event`);if(r.dispatchFlag)throw _e(`This event has been in dispatching.`);if(r.dispatchFlag=!0,r.target=r.currentTarget=this,!r.stopPropagationFlag){let{cow:e,listeners:i}=t;t.cow=!0;for(let a=0;a<i.length;++a){let o=i[a];if(!ke(o)&&(Oe(o)&&Pe(t,a,!e)&&--a,r.inPassiveListenerFlag=De(o),Ae(o,this,n),r.inPassiveListenerFlag=!1,r.stopImmediatePropagationFlag))break}e||(t.cow=!1)}return r.target=null,r.currentTarget=null,r.stopImmediatePropagationFlag=!1,r.stopPropagationFlag=!1,r.dispatchFlag=!1,!r.canceledFlag}},Le=new WeakMap;function Re(e,t=`this`){let n=Le.get(e);return ee(n!=null,`'%s' must be an object that EventTarget constructor created, but got another one: %o`,t,e),n}function ze(e,t,n){return Ve(t),typeof n==`object`&&n?{type:String(e),callback:t??void 0,capture:!!n.capture,passive:!!n.passive,once:!!n.once,signal:n.signal??void 0}:{type:String(e),callback:t??void 0,capture:!!n,passive:!1,once:!1,signal:void 0}}function Be(e,t,n){return Ve(t),typeof n==`object`&&n?{type:String(e),callback:t??void 0,capture:!!n.capture}:{type:String(e),callback:t??void 0,capture:!!n}}function Ve(e){if(!(typeof e==`function`||typeof e==`object`&&e&&typeof e.handleEvent==`function`)){if(e==null||typeof e==`object`)le.warn(e);else throw TypeError(h(le.message,[e]))}}function He(e,t,n,r){se.warn(Ee(e)?`capture`:`bubble`,e.callback),De(e)!==t&&ce.warn(`passive`),Oe(e)!==n&&ce.warn(`once`),e.signal!==r&&ce.warn(`signal`)}var Ue=Object.getOwnPropertyNames(T.prototype);for(let e=0;e<Ue.length;++e)Ue[e]!==`constructor`&&Object.defineProperty(T.prototype,Ue[e],{enumerable:!0});_!==void 0&&_.EventTarget!==void 0&&Object.setPrototypeOf(T.prototype,_.EventTarget.prototype);var We=l(m()),Ge=30720,E=512e3,D=256,O=(e,{minChunkSize:t=D,maxChunkSize:n=E}={})=>e==null||typeof e==`number`&&e>=256&&e%256==0&&e>=t&&e<=n,k=(e,{minChunkSize:t=D,maxChunkSize:n=E}={})=>TypeError(`chunkSize ${e} must be a positive number in multiples of 256, between ${t} and ${n}`),Ke=class{constructor(e,t={}){if(this.readableStream=e,!O(t.defaultChunkSize,t))throw k(t.defaultChunkSize,t);this.defaultChunkSize=t.defaultChunkSize??Ge,this.minChunkSize=t.minChunkSize??D,this.maxChunkSize=t.maxChunkSize??E}get chunkSize(){return this._chunkSize??this.defaultChunkSize}set chunkSize(e){if(!O(e,this))throw k(e,this);this._chunkSize=e}get chunkByteSize(){return this.chunkSize*1024}get error(){return this._error}async*[Symbol.asyncIterator](){let e,t=this.readableStream.getReader();try{for(;;){let{done:n,value:r}=await t.read();if(n){if(e){let t=e;e=void 0,yield t}break}let i=r instanceof Uint8Array?new Blob([r],{type:`application/octet-stream`}):r;for(e=e?new Blob([e,i]):i;e;)if(e.size===this.chunkByteSize){let t=e;e=void 0,yield t;break}else if(e.size<this.chunkByteSize)break;else{let t=e.slice(0,this.chunkByteSize);e=e.slice(this.chunkByteSize),yield t}}}catch(e){this._error=e}finally{if(e){let t=e;e=void 0,yield t}t.releaseLock();return}}},qe=class{constructor(e,t={}){if(this.file=e,!O(t.defaultChunkSize,t))throw k(t.defaultChunkSize,t);this.defaultChunkSize=t.defaultChunkSize??Ge,this.minChunkSize=t.minChunkSize??D,this.maxChunkSize=t.maxChunkSize??E}get chunkSize(){return this._chunkSize??this.defaultChunkSize}set chunkSize(e){if(!O(e,this))throw k(e,this);this._chunkSize=e}get chunkByteSize(){return this.chunkSize*1024}get error(){return this._error}async*[Symbol.asyncIterator](){let e=new FileReader,t=0,n=()=>new Promise(n=>{if(t>=this.file.size){n(void 0);return}let r=Math.min(this.chunkByteSize,this.file.size-t);e.onload=()=>{e.result===null?n(void 0):n(new Blob([e.result],{type:`application/octet-stream`}))},e.readAsArrayBuffer(this.file.slice(t,t+r))});try{for(;;){let e=await n();if(e)t+=e.size,yield e;else break}}catch(e){this._error=e}}},Je=[200,201,202,204,308],Ye=[408,502,503,504],Xe=[308],Ze=(e,t)=>!!e&&Je.includes(e.statusCode),Qe=(e,{retryCodes:t=Ye})=>!e||t.includes(e.statusCode),$e=(e,t)=>t.attemptCount>=t.attempts||!(Ze(e)||Qe(e,t)),et=(e,t)=>{if(!e||!Xe.includes(e.statusCode)||!e.headers?.range)return!1;let n=e.headers.range.match(/bytes=(\d+)-(\d+)/);return n?parseInt(n[2],10)<t.currentChunkEndByte:!1},tt=class{static createUpload(e){return new tt(e)}constructor(e){if(this.eventTarget=new T,this.endpoint=e.endpoint,this.file=e.file,this.headers=e.headers||{},this.method=e.method||`PUT`,this.attempts=e.attempts||5,this.delayBeforeAttempt=e.delayBeforeAttempt||1,this.retryCodes=e.retryCodes||Ye,this.dynamicChunkSize=e.dynamicChunkSize||!1,this.maxFileBytes=(e.maxFileSize||0)*1024,this.chunkCount=0,this.attemptCount=0,this._offline=typeof window<`u`&&!window.navigator.onLine,this._paused=!1,this.success=!1,this.nextChunkRangeStart=0,e.useLargeFileWorkaround){let t=n=>{this.chunkedIterable.error&&(console.warn(`Unable to read file of size ${this.file.size} bytes via a ReadableStream. Falling back to in-memory FileReader!`),n.stopImmediatePropagation(),this.chunkedIterable=new qe(this.file,{...e,defaultChunkSize:e.chunkSize}),this.chunkedIterator=this.chunkedIterable[Symbol.asyncIterator](),this.getEndpoint().then(()=>{this.sendChunks()}).catch(e=>{let t=e?.message?`: ${e.message}`:``;this.dispatch(`error`,{message:`Failed to get endpoint${t}`})}),this.off(`error`,t))};this.on(`error`,t)}this.chunkedIterable=new Ke(this.file.stream(),{...e,defaultChunkSize:e.chunkSize}),this.chunkedIterator=this.chunkedIterable[Symbol.asyncIterator](),this.totalChunks=Math.ceil(this.file.size/this.chunkByteSize),this.validateOptions(),this.getEndpoint().then(()=>this.sendChunks()).catch(e=>{let t=e?.message?`: ${e.message}`:``;this.dispatch(`error`,{message:`Failed to get endpoint${t}`})}),typeof window<`u`&&(window.addEventListener(`online`,()=>{this.offline&&(this._offline=!1,this.dispatch(`online`),this.sendChunks())}),window.addEventListener(`offline`,()=>{this.offline||(this._offline=!0,this.dispatch(`offline`))}))}get maxChunkSize(){return this.chunkedIterable?.maxChunkSize??E}get minChunkSize(){return this.chunkedIterable?.minChunkSize??D}get chunkSize(){return this.chunkedIterable?.chunkSize??Ge}set chunkSize(e){this.chunkedIterable.chunkSize=e}get chunkByteSize(){return this.chunkedIterable.chunkByteSize}get totalChunkSize(){return Math.ceil(this.file.size/this.chunkByteSize)}on(e,t){this.eventTarget.addEventListener(e,t)}once(e,t){this.eventTarget.addEventListener(e,t,{once:!0})}off(e,t){this.eventTarget.removeEventListener(e,t)}get offline(){return this._offline}get paused(){return this._paused}abort(){var e;this.pause(),(e=this.currentXhr)==null||e.abort()}pause(){this._paused=!0}resume(){this._paused&&(this._paused=!1,this.sendChunks())}get successfulPercentage(){return this.nextChunkRangeStart/this.file.size}dispatch(e,t){let n=new CustomEvent(e,{detail:t});this.eventTarget.dispatchEvent(n)}validateOptions(){if(!this.endpoint||typeof this.endpoint!=`function`&&typeof this.endpoint!=`string`)throw TypeError(`endpoint must be defined as a string or a function that returns a promise`);if(!(this.file instanceof File))throw TypeError(`file must be a File object`);if(this.headers&&typeof this.headers!=`function`&&typeof this.headers!=`object`)throw TypeError(`headers must be null, an object, or a function that returns an object or a promise`);if(!O(this.chunkSize,{maxChunkSize:this.maxChunkSize,minChunkSize:this.minChunkSize}))throw k(this.chunkSize,{maxChunkSize:this.maxChunkSize,minChunkSize:this.minChunkSize});if(this.maxChunkSize&&(typeof this.maxChunkSize!=`number`||this.maxChunkSize<256||this.maxChunkSize%256!=0||this.maxChunkSize<this.chunkSize||this.maxChunkSize<this.minChunkSize))throw TypeError(`maxChunkSize must be a positive number in multiples of 256, and larger than or equal to both ${this.minChunkSize} and ${this.chunkSize}`);if(this.minChunkSize&&(typeof this.minChunkSize!=`number`||this.minChunkSize<256||this.minChunkSize%256!=0||this.minChunkSize>this.chunkSize||this.minChunkSize>this.maxChunkSize))throw TypeError(`minChunkSize must be a positive number in multiples of 256, and smaller than ${this.chunkSize} and ${this.maxChunkSize}`);if(this.maxFileBytes>0&&this.maxFileBytes<this.file.size)throw Error(`file size exceeds maximum (${this.file.size} > ${this.maxFileBytes})`);if(this.attempts&&(typeof this.attempts!=`number`||this.attempts<=0))throw TypeError(`retries must be a positive number`);if(this.delayBeforeAttempt&&(typeof this.delayBeforeAttempt!=`number`||this.delayBeforeAttempt<0))throw TypeError(`delayBeforeAttempt must be a positive number`)}getEndpoint(){return typeof this.endpoint==`string`?(this.endpointValue=this.endpoint,Promise.resolve(this.endpoint)):this.endpoint(this.file).then(e=>{if(this.endpointValue=e,typeof e!=`string`)throw TypeError(`endpoint must return a string`);return this.endpointValue})}xhrPromise(e){let t=e=>{e.upload.onprogress=e=>{let t=this.totalChunks-this.chunkCount,n=(this.file.size-this.nextChunkRangeStart)/this.file.size/t,r=e.loaded/(e.total??this.chunkByteSize)*n;this.dispatch(`progress`,Math.min((this.successfulPercentage+r)*100,100))}};return new Promise((n,r)=>{this.currentXhr=(0,We.default)({...e,beforeSend:t},(e,t)=>(this.currentXhr=void 0,e?r(e):n(t)))})}async sendChunk(e){let t=this.nextChunkRangeStart,n=t+e.size-1,r={...await(typeof this.headers==`function`?this.headers():this.headers),"Content-Type":this.file.type,"Content-Range":`bytes ${t}-${n}/${this.file.size}`};return this.dispatch(`attempt`,{chunkNumber:this.chunkCount,totalChunks:this.totalChunks,chunkSize:this.chunkSize}),this.xhrPromise({headers:r,url:this.endpointValue,method:this.method,body:e})}async sendChunkWithRetries(e){let t=async(e,t)=>{let n=(new Date().getTime()-this.lastChunkStart.getTime())/1e3;if(this.dispatch(`chunkSuccess`,{chunk:this.chunkCount,chunkSize:this.chunkSize,attempts:this.attemptCount,timeInterval:n,response:e}),this.attemptCount=0,this.chunkCount=(this.chunkCount??0)+1,this.nextChunkRangeStart+=this.chunkByteSize,this.dynamicChunkSize){let e=this.chunkSize;n<10?e=Math.min(this.chunkSize*2,this.maxChunkSize):n>30&&(e=Math.max(this.chunkSize/2,this.minChunkSize)),this.chunkSize=Math.ceil(e/256)*256;let t=(this.file.size-this.nextChunkRangeStart)/this.chunkByteSize;this.totalChunks=Math.ceil(this.chunkCount+t)}return!0},n=async(e,t)=>(this.dispatch(`progress`,Math.min(this.successfulPercentage*100,100)),this.dispatch(`error`,{message:`Server responded with ${e.statusCode}. Stopping upload.`,chunk:this.chunkCount,attempts:this.attemptCount,response:e}),!1),r=async(t,n)=>(this.dispatch(`attemptFailure`,{message:`An error occured uploading chunk ${this.chunkCount}. ${this.attempts-this.attemptCount} retries left.`,chunkNumber:this.chunkCount,attemptsLeft:this.attempts-this.attemptCount,response:t}),new Promise(t=>{setTimeout(async()=>{this._paused||this.offline?(this.pendingChunk=e,t(!1)):t(await this.sendChunkWithRetries(e))},this.delayBeforeAttempt*1e3)})),i;try{this.attemptCount+=1,this.lastChunkStart=new Date,i=await this.sendChunk(e)}catch(e){typeof e?.statusCode==`number`&&(i=e)}let a={retryCodes:this.retryCodes,attemptCount:this.attemptCount,attempts:this.attempts,currentChunkEndByte:this.nextChunkRangeStart+e.size-1};return et(i,a)?r(i,e):Ze(i,a)?t(i,e):$e(i,a)?n(i,e):r(i,e)}async sendChunks(){if(this.pendingChunk&&!(this._paused||this.offline)){let e=this.pendingChunk;this.pendingChunk=void 0;let t=await this.sendChunkWithRetries(e);this.success&&t&&this.dispatch(`success`)}for(;!(this.success||this._paused||this.offline);){let{value:e,done:t}=await this.chunkedIterator.next(),n=!e&&t;if(e&&(n=await this.sendChunkWithRetries(e)),this.chunkedIterable.error){n=!1,this.dispatch(`error`,{message:`Unable to read file of size ${this.file.size} bytes. Try loading from another browser.`});return}if(this.success=!!t,this.success&&n&&this.dispatch(`success`),!n)return}}},nt=e({MuxUploaderDropElement:()=>Et,MuxUploaderFileSelectElement:()=>Wt,MuxUploaderPauseElement:()=>Bt,MuxUploaderProgressElement:()=>jt,MuxUploaderRetryElement:()=>Lt,MuxUploaderSrTextElement:()=>$t,MuxUploaderStatusElement:()=>Pt,constants:()=>st,default:()=>en,t:()=>P}),rt=Object.defineProperty,it=e=>{throw TypeError(e)},at=(e,t)=>{for(var n in t)rt(e,n,{get:t[n],enumerable:!0})},ot=(e,t,n)=>t.has(e)||it(`Cannot `+n),A=(e,t,n)=>(ot(e,t,`read from private field`),n?n.call(e):t.get(e)),j=(e,t,n)=>t.has(e)?it(`Cannot add the same private member more than once`):t instanceof WeakSet?t.add(e):t.set(e,n),M=(e,t,n,r)=>(ot(e,t,`write to private field`),r?r.call(e,n):t.set(e,n),n),st={};at(st,{ProgressTypes:()=>N});var N={BAR:`bar`,RADIAL:`radial`,PERCENTAGE:`percentage`},ct={"Drop a video file here to upload":`Drop a video file here to upload`,or:`or`,"Upload complete!":`Upload complete!`,Retry:`Retry`,"Pausing...":`Pausing...`,Resume:`Resume`,Pause:`Pause`,"Upload a video":`Upload a video`,"No url or endpoint specified - cannot handle upload":`No url or endpoint specified - cannot handle upload`},lt={en:ct,es:{"Drop a video file here to upload":`Arrastra un archivo de video aquí para subir`,or:`o`,"Upload complete!":`¡Subida completada!`,Retry:`Reintentar`,"Pausing...":`Pausando...`,Resume:`Reanudar`,Pause:`Pausar`,"Upload a video":`Subir un video`,"No url or endpoint specified - cannot handle upload":`No se especificó URL o endpoint - no se puede manejar la subida`},fr:{"Drop a video file here to upload":`Déposez un fichier vidéo ici pour le télécharger`,or:`ou`,"Upload complete!":`Téléchargement terminé!`,Retry:`Réessayer`,"Pausing...":`En pause...`,Resume:`Reprendre`,Pause:`Pause`,"Upload a video":`Télécharger une vidéo`,"No url or endpoint specified - cannot handle upload":`Aucune URL ou point de terminaison spécifié - impossible de gérer le téléchargement`},de:{"Drop a video file here to upload":`Legen Sie hier eine Videodatei zum Hochladen ab`,or:`oder`,"Upload complete!":`Upload abgeschlossen!`,Retry:`Wiederholen`,"Pausing...":`Pausiere...`,Resume:`Fortsetzen`,Pause:`Pausieren`,"Upload a video":`Video hochladen`,"No url or endpoint specified - cannot handle upload":`Keine URL oder Endpunkt angegeben - Upload kann nicht verarbeitet werden`},pt:{"Drop a video file here to upload":`Arraste um arquivo de vídeo aqui para fazer o upload`,or:`ou`,"Upload complete!":`Upload completo!`,Retry:`Tentar novamente`,"Pausing...":`Pausando...`,Resume:`Retomar`,Pause:`Pausar`,"Upload a video":`Fazer upload de um vídeo`,"No url or endpoint specified - cannot handle upload":`Nenhum URL ou endpoint especificado - não é possível processar o upload`},it:{"Drop a video file here to upload":`Trascina qui un file video per caricarlo`,or:`o`,"Upload complete!":`Caricamento completato!`,Retry:`Riprova`,"Pausing...":`In pausa...`,Resume:`Riprendi`,Pause:`Pausa`,"Upload a video":`Carica un video`,"No url or endpoint specified - cannot handle upload":`Nessun URL o endpoint specificato - impossibile gestire il caricamento`},zh:{"Drop a video file here to upload":`将视频文件拖放到此处以上传`,or:`或`,"Upload complete!":`上传完成！`,Retry:`重试`,"Pausing...":`暂停...`,Resume:`继续`,Pause:`暂停`,"Upload a video":`上传视频`,"No url or endpoint specified - cannot handle upload":`未指定 URL 或 endpoint - 无法处理上传`}},ut=()=>globalThis.navigator===void 0||!globalThis.navigator.language?`en`:globalThis.navigator.language.split(`-`)[0],dt=[`en`,`es`,`fr`,`de`,`pt`,`it`,`zh`],ft=e=>{if(e&&dt.includes(e))return e;let t=ut();return dt.includes(t)?t:`en`},P=(e,t)=>(lt[ft(t)]||ct)[e]||ct[e],F=class{addEventListener(){}removeEventListener(){}dispatchEvent(e){return!0}};if(typeof DocumentFragment>`u`){class e extends F{}globalThis.DocumentFragment=e}var pt=class extends F{},mt=class extends F{},ht={get(e){},define(e,t,n){},getName(e){return null},upgrade(e){},whenDefined(e){return Promise.resolve(pt)}},gt,_t=class{constructor(e,t={}){j(this,gt),M(this,gt,t?.detail)}get detail(){return A(this,gt)}initCustomEvent(){}};gt=new WeakMap;function vt(e,t){return new pt}var yt={document:{createElement:vt},DocumentFragment,customElements:ht,CustomEvent:_t,EventTarget:F,HTMLElement:pt,HTMLVideoElement:mt},bt=typeof window>`u`||globalThis.customElements===void 0,I=bt?yt:globalThis,L=bt?yt.document:globalThis.document,xt=(e,t)=>e?e.closest(t)||xt(e.getRootNode().host,t):null,R=e=>{let t=e.getAttribute(`mux-uploader`);return t?document.getElementById(t):xt(e,`mux-uploader`)},St=L.createElement(`template`);St.innerHTML=`
<style>
  :host {
    position: relative;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 2px dashed #ccc;
    padding: 2.5rem 2rem;
    border-radius: .25rem;
  }

  slot[name='heading'] > * {
    margin-bottom: 0.75rem;
    font-size: 1.75rem;
    text-align: center;
  }

  slot[name='separator'] > * {
    margin-bottom: 0.75rem;
  }

  #overlay {
    display: none;
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    left: 0;
    height: 100%;
    width: 100%;
  }

  :host([active][overlay]) > #overlay {
    background: var(--overlay-background-color, rgba(226, 253, 255, 0.95));
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  :host([file-ready])::part(heading),
  :host([file-ready])::part(separator) {
    display: none;
  }
</style>

<slot name="heading" part="heading">
  <span id="drop-text">Drop a video file here to upload</span>
</slot>
<slot name="separator" part="separator">
  <span id="separator-text">or</span>
</slot>
<slot></slot>

<div id="overlay">
  <h1 id="overlay-label"></h1>
</div>
`;var Ct={MUX_UPLOADER:`mux-uploader`,OVERLAY_TEXT:`overlay-text`},wt,z,B,Tt=class extends I.HTMLElement{constructor(){super(),j(this,wt),j(this,z),j(this,B);let e=this.attachShadow({mode:`open`});e.appendChild(St.content.cloneNode(!0)),M(this,wt,e.getElementById(`overlay-label`))}connectedCallback(){if(M(this,z,R(this)),M(this,B,new AbortController),A(this,z)){let e={signal:A(this,B).signal};A(this,z).addEventListener(`file-ready`,()=>this.toggleAttribute(`file-ready`,!0),e),A(this,z).addEventListener(`uploadstart`,()=>this.toggleAttribute(`upload-in-progress`,!0),e),A(this,z).addEventListener(`success`,()=>{this.toggleAttribute(`upload-in-progress`,!1),this.toggleAttribute(`upload-complete`,!0)},e),A(this,z).addEventListener(`reset`,()=>{this.toggleAttribute(`file-ready`,!1),this.toggleAttribute(`upload-in-progress`,!1),this.toggleAttribute(`upload-complete`,!1)},e),this.setupDragEvents(e),this.toggleAttribute(`upload-in-progress`,A(this,z).hasAttribute(`upload-in-progress`)),this.toggleAttribute(`upload-complete`,A(this,z).hasAttribute(`upload-complete`)),this.toggleAttribute(`file-ready`,A(this,z).hasAttribute(`file-ready`)),A(this,z).addEventListener(`localechange`,()=>this.updateText(),e),this.updateText()}}disconnectedCallback(){var e;(e=A(this,B))==null||e.abort()}attributeChangedCallback(e,t,n){e===Ct.OVERLAY_TEXT&&t!==n?A(this,wt).textContent=n??``:e===`active`&&this.hasAttribute(`overlay`)&&n!=null&&(this._currentDragTarget=this)}static get observedAttributes(){return[Ct.OVERLAY_TEXT,Ct.MUX_UPLOADER,`active`]}setupDragEvents(e){this.addEventListener(`dragenter`,e=>{this._currentDragTarget=e.target,e.preventDefault(),e.stopPropagation(),this.toggleAttribute(`active`,!0)},e),this.addEventListener(`dragleave`,e=>{this._currentDragTarget===e.target&&(this._currentDragTarget=void 0,this.toggleAttribute(`active`,!1))},e),this.addEventListener(`dragover`,e=>{e.preventDefault(),e.stopPropagation()},e),this.addEventListener(`drop`,e=>{e.preventDefault(),e.stopPropagation();let{dataTransfer:t}=e,{files:n}=t,r=n[0];(A(this,z)??this).dispatchEvent(new CustomEvent(`file-ready`,{composed:!0,bubbles:!0,detail:r})),this.removeAttribute(`active`)},e)}updateText(){let e=A(this,z)?.locale,t=this.shadowRoot?.getElementById(`drop-text`),n=this.shadowRoot?.getElementById(`separator-text`);t&&(t.textContent=P(`Drop a video file here to upload`,e)),n&&(n.textContent=P(`or`,e))}};wt=new WeakMap,z=new WeakMap,B=new WeakMap,I.customElements.get(`mux-uploader-drop`)||(I.customElements.define(`mux-uploader-drop`,Tt),I.MuxUploaderDropElement=Tt);var Et=Tt;function Dt(e){return`${Math.floor(e)}%`}var Ot=L.createElement(`template`),kt=`Media upload progress bar`;Ot.innerHTML=`
<style>
  :host {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
  }

  .bar-type {
    background: var(--progress-bar-background-color, #e6e6e6);
    border-radius: var(--progress-bar-border-radius, 100px);
    height: var(--progress-bar-height, 4px);
    width: 100%;
  }

  .radial-type,
  .bar-type,
  #percentage-type,
  :host([type="bar"][upload-error]) #percentage-type {
    display: none;
  }

  :host([type="radial"][upload-in-progress]) .radial-type,
  :host([type="bar"][upload-in-progress]) .bar-type {
    display: block;
  }

  :host([type="percentage"][upload-in-progress]) #percentage-type {
    display: var(--progress-percentage-display, block);
  }

  :host([type="bar"][upload-error]) .progress-bar {
    background: #e22c3e;
  }

  .progress-bar {
    box-shadow: var(--progress-bar-box-shadow, 0 10px 40px -10px #fff);
    border-radius: var(--progress-bar-border-radius, 100px);
    background: var(--progress-bar-fill-color, #000000);
    height: var(--progress-bar-height, 4px);
    width: 0%;
    transition: width 0.25s;
  }

  circle {
    stroke: var(--progress-radial-fill-color, black);
    stroke-width: 6;  /* Thickness of the circle */
    fill: transparent; /* Make inside of the circle see-through */

    /* Animation */
    transition: 0.35s;
    transform: rotate(-90deg);
    transform-origin: 50% 50%;
    -webkit-transform-origin: 50% 50%;
    -moz-transform-origin: 50% 50%;
  }

  #percentage-type {
    font-size: inherit;
    margin: 0 0 1em;
  }
</style>

<slot></slot>

<p id="percentage-type"></p>
<div class="bar-type">
  <div role="progressbar" aria-valuemin="0" aria-valuemax="100" class="progress-bar" id="progress-bar" tabindex="0"></div>
</div>
<div class="radial-type">
  <svg
    width="120"
    height="120">
    <!-- To prevent overflow of the SVG wrapper, radius must be  (svgWidth / 2) - (circleStrokeWidth * 2)
      or use overflow: visible on the svg.-->
    <circle
      r="52"
      cx="60"
      cy="60"
    />
  <svg>
</div>
`;var V,H,At=class extends I.HTMLElement{constructor(){var e;super(),j(this,V),j(this,H),this.onUploadStart=()=>{var e;(e=this.progressBar)==null||e.focus(),this.toggleAttribute(`upload-in-progress`,!0)},this.onProgress=e=>{var t;let n=e.detail;switch((t=this.progressBar)==null||t.setAttribute(`aria-valuenow`,`${Math.floor(n)}`),this.getAttribute(`type`)){case N.BAR:this.progressBar&&(this.progressBar.style.width=`${n}%`);break;case N.RADIAL:if(this.svgCircle){let e=this.getCircumference()-n/100*this.getCircumference();this.svgCircle.style.strokeDashoffset=e.toString()}break;case N.PERCENTAGE:this.uploadPercentage&&(this.uploadPercentage.innerHTML=Dt(n))}},this.onSuccess=()=>{this.toggleAttribute(`upload-in-progress`,!1),this.toggleAttribute(`upload-complete`,!0)},this.onReset=()=>{this.toggleAttribute(`upload-in-progress`,!1),this.uploadPercentage&&(this.uploadPercentage.innerHTML=``),this.svgCircle&&(this.svgCircle.style.strokeDashoffset=`${this.getCircumference()}`)},this.attachShadow({mode:`open`}).appendChild(Ot.content.cloneNode(!0)),this.svgCircle=this.shadowRoot?.querySelector(`circle`),this.progressBar=this.shadowRoot?.getElementById(`progress-bar`),this.uploadPercentage=this.shadowRoot?.getElementById(`percentage-type`),(e=this.progressBar)==null||e.setAttribute(`aria-description`,kt)}connectedCallback(){if(this.setDefaultType(),M(this,V,R(this)),M(this,H,new AbortController),A(this,V)){let e={signal:A(this,H).signal};A(this,V).addEventListener(`uploadstart`,this.onUploadStart,e),A(this,V).addEventListener(`reset`,this.onReset),A(this,V).addEventListener(`progress`,this.onProgress),A(this,V).addEventListener(`success`,this.onSuccess),this.toggleAttribute(`upload-in-progress`,A(this,V).hasAttribute(`upload-in-progress`)),this.toggleAttribute(`upload-complete`,A(this,V).hasAttribute(`upload-complete`))}}disconnectedCallback(){var e;(e=A(this,H))==null||e.abort()}getRadius(){return Number(this.svgCircle?.getAttribute(`r`))}getCircumference(){return this.getRadius()*2*Math.PI}setDefaultType(){let e=this.getAttribute(`type`);e||this.setAttribute(`type`,N.BAR),e===N.RADIAL&&this.svgCircle&&(this.svgCircle.style.strokeDasharray=`${this.getCircumference()} ${this.getCircumference()}`,this.svgCircle.style.strokeDashoffset=`${this.getCircumference()}`)}};V=new WeakMap,H=new WeakMap,I.customElements.get(`mux-uploader-progress`)||I.customElements.define(`mux-uploader-progress`,At);var jt=At,Mt=L.createElement(`template`);Mt.innerHTML=`
<style>

:host([upload-error]) {
  color: #e22c3e;
}
</style>

<span id="status-message" role="status" aria-live="polite"></span>
`;var U,W,Nt=class extends I.HTMLElement{constructor(){super(),j(this,U),j(this,W),this.clearStatusMessage=()=>{this.toggleAttribute(`upload-error`,!1),this.statusMessage&&(this.statusMessage.textContent=``)},this.onUploadError=e=>{this.toggleAttribute(`upload-error`,!0),this.statusMessage&&(this.statusMessage.textContent=e.detail.message)},this.onSuccess=()=>{this.toggleAttribute(`upload-error`,!1);let e=P(`Upload complete!`,A(this,U)?.locale);this.statusMessage&&(this.statusMessage.textContent=e),console.info(e)},this.onOffline=()=>{this.toggleAttribute(`upload-error`,!1),this.statusMessage&&(this.statusMessage.textContent=`Currently offline. Upload will resume automatically when online.`)},this.attachShadow({mode:`open`}).appendChild(Mt.content.cloneNode(!0)),this.statusMessage=this.shadowRoot?.getElementById(`status-message`)}connectedCallback(){if(M(this,U,R(this)),M(this,W,new AbortController),A(this,U)){let e={signal:A(this,W).signal};A(this,U).addEventListener(`reset`,this.clearStatusMessage,e),A(this,U).addEventListener(`uploaderror`,this.onUploadError,e),A(this,U).addEventListener(`success`,this.onSuccess,e),A(this,U).addEventListener(`uploadstart`,this.clearStatusMessage,e),A(this,U).addEventListener(`offline`,this.onOffline,e),A(this,U).addEventListener(`online`,this.clearStatusMessage,e),this.toggleAttribute(`upload-in-progress`,A(this,U).hasAttribute(`upload-in-progress`)),this.toggleAttribute(`upload-complete`,A(this,U).hasAttribute(`upload-complete`)),this.toggleAttribute(`upload-error`,A(this,U).hasAttribute(`upload-error`)),A(this,U).addEventListener(`localechange`,()=>{var e,t;if((e=this.statusMessage)!=null&&e.textContent&&(t=A(this,U))!=null&&t.hasAttribute(`upload-complete`)){let e=A(this,U)?.locale;this.statusMessage.textContent=P(`Upload complete!`,e)}},e)}}disconnectedCallback(){var e;(e=A(this,W))==null||e.abort()}};U=new WeakMap,W=new WeakMap,I.customElements.get(`mux-uploader-status`)||I.customElements.define(`mux-uploader-status`,Nt);var Pt=Nt,Ft=L.createElement(`template`);Ft.innerHTML=`
<style>
  #retry-button {
    color: #e22c3e;
    text-decoration-line: underline;
    cursor: pointer;
    position: relative;
    display: none;
  }

  :host([upload-error]) #retry-button {
    display: inline-block;
  }
</style>

<span id="retry-button" role="button" tabindex="0">Try again</span>
`;var G,K,It=class extends I.HTMLElement{constructor(){super(),j(this,G),j(this,K),this.handleKeyup=e=>{let t=[`Enter`,` `],{key:n}=e;t.includes(n)&&this.triggerReset()},this.triggerReset=()=>{var e;(e=A(this,G))==null||e.dispatchEvent(new CustomEvent(`reset`))},this.attachShadow({mode:`open`}).appendChild(Ft.content.cloneNode(!0)),this.retryButton=this.shadowRoot?.getElementById(`retry-button`)}connectedCallback(){var e,t;if(M(this,G,R(this)),M(this,K,new AbortController),A(this,G)){let n={signal:A(this,K).signal};A(this,G).addEventListener(`uploaderror`,()=>this.toggleAttribute(`upload-error`,!0)),A(this,G).addEventListener(`reset`,()=>this.toggleAttribute(`upload-error`,!1)),(e=this.retryButton)==null||e.addEventListener(`click`,this.triggerReset,n),(t=this.retryButton)==null||t.addEventListener(`keyup`,this.handleKeyup,n),this.toggleAttribute(`upload-error`,A(this,G).hasAttribute(`upload-error`)),A(this,G).addEventListener(`localechange`,()=>this.updateText(),n),this.updateText()}}disconnectedCallback(){var e;(e=A(this,K))==null||e.abort()}updateText(){let e=A(this,G)?.locale;this.retryButton&&(this.retryButton.textContent=P(`Retry`,e))}};G=new WeakMap,K=new WeakMap,I.customElements.get(`mux-uploader-retry`)||I.customElements.define(`mux-uploader-retry`,It);var Lt=It,Rt=L.createElement(`template`);Rt.innerHTML=`
<style>
#pause-button {
  cursor: pointer;
  line-height: 16px;
  background: #fff;
  border: 1px solid #000;
  color: #000000;
  padding: 16px 24px;
  border-radius: 4px;
  -webkit-transition: all 0.2s ease;
  transition: all 0.2s ease;
  font-family: inherit;
  font-size: inherit;
  position: relative;
  display: none;
}

#pause-button:hover:not(:disabled) {
  color: #fff;
  background: #404040;
}

#pause-button:active {
  color: #fff;
  background: #000;
}

#pause-button:disabled {
  cursor: not-allowed;
}

:host([upload-in-progress]:not([upload-error], [upload-complete])) #pause-button {
  display: initial;
}
</style>

<button id="pause-button">Pause</span>
`;var q,J,zt=class extends I.HTMLElement{constructor(){super(),j(this,q),j(this,J),this.triggerPause=()=>{A(this,q)?this.pauseButton.disabled||(A(this,q).paused=!A(this,q).paused):console.warn(`pausing before a mux-uploader element is associated is unsupported!`)},this.attachShadow({mode:`open`}).appendChild(Rt.content.cloneNode(!0))}connectedCallback(){if(M(this,q,R(this)),M(this,J,new AbortController),A(this,q)){let e={signal:A(this,J).signal};A(this,q).addEventListener(`uploadstart`,()=>this.toggleAttribute(`upload-in-progress`,!0),e),A(this,q).addEventListener(`uploaderror`,()=>{this.toggleAttribute(`upload-error`,!0),this.toggleAttribute(`upload-complete`,!1),this.toggleAttribute(`upload-in-progress`,!1)}),A(this,q).addEventListener(`success`,()=>{this.toggleAttribute(`upload-complete`,!0),this.toggleAttribute(`upload-error`,!1),this.toggleAttribute(`upload-in-progress`,!1)}),A(this,q).addEventListener(`reset`,()=>{this.toggleAttribute(`upload-error`,!1),this.toggleAttribute(`upload-in-progress`,!1),this.toggleAttribute(`upload-complete`,!1)}),A(this,q).addEventListener(`pausedchange`,()=>{if(this.pauseButton.disabled=!1,!A(this,q))return;let e=A(this,q).paused??!1;this.updateText(),e&&(this.pauseButton.disabled=!0,A(this,q).addEventListener(`chunksuccess`,()=>{this.updateText(),this.pauseButton.disabled=!1},{once:!0}))}),this.pauseButton.addEventListener(`click`,this.triggerPause,e),this.toggleAttribute(`upload-in-progress`,A(this,q).hasAttribute(`upload-in-progress`)),this.toggleAttribute(`upload-complete`,A(this,q).hasAttribute(`upload-complete`)),this.toggleAttribute(`upload-error`,A(this,q).hasAttribute(`upload-error`)),A(this,q).addEventListener(`localechange`,()=>this.updateText(),e),this.updateText()}}disconnectedCallback(){var e;(e=A(this,J))==null||e.abort()}get pauseButton(){return this.shadowRoot?.getElementById(`pause-button`)}updateText(){let e=A(this,q)?.locale,t=A(this,q)?.paused??!1;this.pauseButton?.disabled&&t?this.pauseButton.innerHTML=P(`Pausing...`,e):this.pauseButton.innerHTML=P(t?`Resume`:`Pause`,e)}};q=new WeakMap,J=new WeakMap,I.customElements.get(`mux-uploader-pause`)||I.customElements.define(`mux-uploader-pause`,zt);var Bt=zt,Vt=`
  <style>
  #file-select {
    cursor: pointer;
    line-height: 16px;
    background: #fff;
    border: 1px solid #000;
    color: #000000;
    padding: 16px 24px;
    border-radius: 4px;
    -webkit-transition: all 0.2s ease;
    transition: all 0.2s ease;
    font-family: inherit;
    font-size: inherit;
    position: relative;
  }

  #file-select:hover {
    color: #fff;
    background: #404040;
  }

  #file-select:active {
    color: #fff;
    background: #000;
  }

  </style>

  <button id="file-select" type="button" part="file-select-button">Upload a video</button>
`,Ht=L.createElement(`template`);Ht.innerHTML=`
  <style>
    :host { display: inline-block; }

    :host([file-ready]) > slot  {
      display: none;
    }
  </style>

  <slot>
    ${Vt}
  </slot>
`;var Y,X,Z,Ut=class extends I.HTMLElement{constructor(){var e;super(),j(this,Y),j(this,X),j(this,Z),this.attachShadow({mode:`open`}).appendChild(Ht.content.cloneNode(!0)),this.handleFilePickerElClick=this.handleFilePickerElClick.bind(this),this.filePickerEl=this.shadowRoot?.querySelector(`button`),(e=this.shadowRoot?.querySelector(`slot`))==null||e.addEventListener(`slotchange`,e=>{let t=e.currentTarget;this.filePickerEl=t.assignedElements({flatten:!0}).filter(e=>![`STYLE`].includes(e.nodeName))[0],this.updateText()})}connectedCallback(){if(M(this,X,R(this)),M(this,Z,new AbortController),A(this,X)){let e={signal:A(this,Z).signal};A(this,X).addEventListener(`file-ready`,()=>{this.toggleAttribute(`file-ready`,!0)},e),A(this,X).addEventListener(`uploadstart`,()=>this.toggleAttribute(`upload-in-progress`,!0),e),A(this,X).addEventListener(`success`,()=>{this.toggleAttribute(`upload-in-progress`,!1),this.toggleAttribute(`upload-complete`,!0)},e),A(this,X).addEventListener(`reset`,()=>{this.toggleAttribute(`file-ready`,!1)},e),this.toggleAttribute(`upload-in-progress`,A(this,X).hasAttribute(`upload-in-progress`)),this.toggleAttribute(`upload-complete`,A(this,X).hasAttribute(`upload-complete`)),this.toggleAttribute(`file-ready`,A(this,X).hasAttribute(`file-ready`)),A(this,X).addEventListener(`localechange`,()=>this.updateText(),e),this.updateText()}}disconnectedCallback(){var e;(e=A(this,Z))==null||e.abort()}get filePickerEl(){return A(this,Y)}set filePickerEl(e){e!==A(this,Y)&&(A(this,Y)&&A(this,Y).removeEventListener(`click`,this.handleFilePickerElClick),M(this,Y,e),A(this,Y)&&A(this,Y).addEventListener(`click`,this.handleFilePickerElClick))}handleFilePickerElClick(){var e;let t=this.getAttribute(`mux-uploader`);(e=((t?L.getElementById(t):this.getRootNode().host)?.shadowRoot)?.querySelector(`#hidden-file-input`))==null||e.click()}updateText(){let e=P(`Upload a video`,A(this,X)?.locale),t=this.shadowRoot?.querySelector(`#file-select`);t&&(t.textContent=e)}};Y=new WeakMap,X=new WeakMap,Z=new WeakMap,I.customElements.get(`mux-uploader-file-select`)||I.customElements.define(`mux-uploader-file-select`,Ut);var Wt=Ut;function Q(e,t){return e?``:t}function Gt(e){return e.replace(/&/g,`&amp;`).replace(/</g,`&lt;`).replace(/>/g,`&gt;`).replace(/"/g,`&quot;`).replace(/'/g,`&#39;`)}var Kt=(e,t)=>t==null||t===!1?``:`${e}="${Gt(t===!0?``:`${t}`)}"`;function qt(e){let{noDrop:t,noProgress:n,noStatus:r,noRetry:i,pausable:a,type:o}=e,s=t?`div`:`mux-uploader-drop overlay part="drop"`,c=Q(n,`
      <mux-uploader-progress part="progress progress-percentage" type="percentage"></mux-uploader-progress>
      <mux-uploader-progress part="progress progress-bar" ${Kt(`type`,o)}></mux-uploader-progress>
    `),l=Q(r,`<mux-uploader-status part="status"></mux-uploader-status>`),u=Q(i,`<mux-uploader-retry part="retry"></mux-uploader-retry>`),d=Q(!a,`<mux-uploader-pause part="pause"></mux-uploader-pause>`);return L.createRange().createContextualFragment(`
    <${s}>
      ${l}
      ${u}
      ${d}

      <mux-uploader-file-select part="file-select">
        <slot name="file-select">
          ${Vt}
        </slot>
      </mux-uploader-file-select>

      ${c}
    </${s}>
  `)}var Jt=L.createElement(`template`);Jt.innerHTML=`
<style>
  :host {
    display: flex;
    flex-direction: column;
  }

  mux-uploader-drop {
    flex-grow: 1;
  }

  input[type="file"] {
    display: none;
  }
</style>

<input id="hidden-file-input" type="file" accept="video/*, audio/*" />
<mux-uploader-sr-text></mux-uploader-sr-text>
`;var Yt=class extends I.HTMLElement{static get observedAttributes(){return[`pausable`,`type`,`no-drop`,`no-progress`,`no-status`,`no-retry`,`max-file-size`,`use-large-file-workaround`,`locale`]}constructor(){var e;super(),this.attachShadow({mode:`open`}).appendChild(Jt.content.cloneNode(!0)),this.updateLayout(),(e=this.hiddenFileInput)==null||e.addEventListener(`change`,()=>{let e=this.hiddenFileInput?.files?.[0];this.toggleAttribute(`file-ready`,!!e),e&&this.dispatchEvent(new CustomEvent(`file-ready`,{composed:!0,bubbles:!0,detail:e}))})}connectedCallback(){this.addEventListener(`file-ready`,this.handleUpload),this.addEventListener(`reset`,this.resetState)}disconnectedCallback(){this.removeEventListener(`file-ready`,this.handleUpload,!1),this.removeEventListener(`reset`,this.resetState)}attributeChangedCallback(e,t,n){e===`locale`&&t!==n?this.dispatchEvent(new CustomEvent(`localechange`,{detail:{locale:n}})):e!==`locale`&&this.updateLayout()}get hiddenFileInput(){return this.shadowRoot?.querySelector(`#hidden-file-input`)}get endpoint(){return this.getAttribute(`endpoint`)??this._endpoint}set endpoint(e){e!==this.endpoint&&(typeof e==`string`?this.setAttribute(`endpoint`,e):e??this.removeAttribute(`endpoint`),this._endpoint=e)}get type(){return this.getAttribute(`type`)??void 0}set type(e){e!=this.type&&(e?this.setAttribute(`type`,e):this.removeAttribute(`type`))}get noDrop(){return this.hasAttribute(`no-drop`)}set noDrop(e){this.toggleAttribute(`no-drop`,!!e)}get noProgress(){return this.hasAttribute(`no-progress`)}set noProgress(e){this.toggleAttribute(`no-progress`,!!e)}get noStatus(){return this.hasAttribute(`no-status`)}set noStatus(e){this.toggleAttribute(`no-status`,!!e)}get noRetry(){return this.hasAttribute(`no-retry`)}set noRetry(e){this.toggleAttribute(`no-retry`,!!e)}get pausable(){return this.hasAttribute(`pausable`)}set pausable(e){this.toggleAttribute(`pausable`,!!e)}get dynamicChunkSize(){return this.hasAttribute(`dynamic-chunk-size`)}set dynamicChunkSize(e){e!==this.hasAttribute(`dynamic-chunk-size`)&&(e?this.setAttribute(`dynamic-chunk-size`,``):this.removeAttribute(`dynamic-chunk-size`))}get useLargeFileWorkaround(){return this.hasAttribute(`use-large-file-workaround`)}set useLargeFileWorkaround(e){e!=this.useLargeFileWorkaround&&this.toggleAttribute(`use-large-file-workaround`,!!e)}get maxFileSize(){let e=this.getAttribute(`max-file-size`);return e===null?void 0:parseInt(e)}set maxFileSize(e){e?this.setAttribute(`max-file-size`,e.toString()):this.removeAttribute(`max-file-size`)}get chunkSize(){let e=this.getAttribute(`chunk-size`);return e===null?void 0:parseInt(e)}set chunkSize(e){e?this.setAttribute(`chunk-size`,e.toString()):this.removeAttribute(`chunk-size`)}get locale(){return this.getAttribute(`locale`)}set locale(e){e!==this.locale&&(e?this.setAttribute(`locale`,e):this.removeAttribute(`locale`))}get upload(){return this._upload}get paused(){return this.upload?.paused??!1}set paused(e){if(!this.upload){console.warn(`Pausing before an upload has begun is unsupported`);return}let t=!!e;t!==this.paused&&(t?this.upload.pause():this.upload.resume(),this.toggleAttribute(`paused`,t),this.dispatchEvent(new CustomEvent(`pausedchange`,{detail:t})))}updateLayout(){var e;let t=this.shadowRoot?.querySelector(`mux-uploader-drop, div`);t&&t.remove();let n=qt(this);(e=this.shadowRoot)==null||e.appendChild(n)}setError(e){this.setAttribute(`upload-error`,``),this.dispatchEvent(new CustomEvent(`uploaderror`,{detail:{message:e}}))}resetState(){this.removeAttribute(`upload-error`),this.removeAttribute(`upload-in-progress`),this.removeAttribute(`upload-complete`),this.hiddenFileInput.value=``}handleUpload(e){let t=this.endpoint,n=this.dynamicChunkSize;if(t)this.removeAttribute(`upload-error`);else{this.setError(P(`No url or endpoint specified - cannot handle upload`,this.locale));return}try{let r=tt.createUpload({endpoint:t,dynamicChunkSize:n,file:e.detail,maxFileSize:this.maxFileSize,chunkSize:this.chunkSize,useLargeFileWorkaround:this.useLargeFileWorkaround});this._upload=r,this.dispatchEvent(new CustomEvent(`uploadstart`,{detail:{file:r.file,chunkSize:r.chunkSize}})),this.setAttribute(`upload-in-progress`,``),r.offline&&this.dispatchEvent(new CustomEvent(`offline`)),r.on(`attempt`,e=>{this.dispatchEvent(new CustomEvent(`chunkattempt`,e))}),r.on(`chunkSuccess`,e=>{this.dispatchEvent(new CustomEvent(`chunksuccess`,e))}),r.on(`error`,e=>{this.setAttribute(`upload-error`,``),console.error(`error handler`,e.detail.message),this.dispatchEvent(new CustomEvent(`uploaderror`,e))}),r.on(`progress`,e=>{this.dispatchEvent(new CustomEvent(`progress`,e))}),r.on(`success`,e=>{this.removeAttribute(`upload-in-progress`),this.setAttribute(`upload-complete`,``),this.dispatchEvent(new CustomEvent(`success`,e))}),r.on(`offline`,e=>{this.dispatchEvent(new CustomEvent(`offline`,e))}),r.on(`online`,e=>{this.dispatchEvent(new CustomEvent(`online`,e))})}catch(e){e instanceof Error&&this.setError(e.message)}}};I.customElements.get(`mux-uploader`)||(I.customElements.define(`mux-uploader`,Yt),I.MuxUploaderElement=Yt);var Xt=Yt,Zt=L.createElement(`template`);Zt.innerHTML=`
<style>

.sr-only {
  position:absolute;
  left:-10000px;
  top:auto;
  width:1px;
  height:1px;
  overflow:hidden;
}
</style>

<div class="sr-only" id="sr-only" aria-live="polite"></div>
`;var $,Qt=class extends I.HTMLElement{constructor(){super(),j(this,$),this.attachShadow({mode:`open`}).appendChild(Zt.content.cloneNode(!0)),this.srOnlyText=this.shadowRoot?.getElementById(`sr-only`)}connectedCallback(){M(this,$,R(this)),A(this,$)&&(A(this,$).addEventListener(`success`,this.updateText.bind(this)),A(this,$).addEventListener(`localechange`,()=>{var e;(e=this.srOnlyText)!=null&&e.textContent&&this.updateText()}))}disconnectedCallback(){A(this,$)&&A(this,$).removeEventListener(`success`,this.updateText.bind(this))}updateText(){if(this.srOnlyText){let e=A(this,$)?.locale;this.srOnlyText.textContent=P(`Upload complete!`,e)}}};$=new WeakMap,I.customElements.get(`mux-uploader-sr-text`)||I.customElements.define(`mux-uploader-sr-text`,Qt);var $t=Qt,en=Xt;export{nt as t};