!function(e){var t={};function n(i){if(t[i])return t[i].exports;var a=t[i]={i:i,l:!1,exports:{}};return e[i].call(a.exports,a,a.exports,n),a.l=!0,a.exports}n.m=e,n.c=t,n.d=function(e,t,i){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)n.d(i,a,function(t){return e[t]}.bind(null,a));return i},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";n.r(t),n.d(t,"measureWebVitals",(function(){return M})),n.d(t,"initAnalytics",(function(){return I}));var i,a,o,r,s=function(e,t){return{name:e,value:void 0===t?-1:t,delta:0,entries:[],id:"v1-".concat(Date.now(),"-").concat(Math.floor(8999999999999*Math.random())+1e12)}},c=function(e,t){try{if(PerformanceObserver.supportedEntryTypes.includes(e)){var n=new PerformanceObserver((function(e){return e.getEntries().map(t)}));return n.observe({type:e,buffered:!0}),n}}catch(e){}},u=function(e,t){var n=function n(i){"pagehide"!==i.type&&"hidden"!==document.visibilityState||(e(i),t&&(removeEventListener("visibilitychange",n,!0),removeEventListener("pagehide",n,!0)))};addEventListener("visibilitychange",n,!0),addEventListener("pagehide",n,!0)},d=function(e){addEventListener("pageshow",(function(t){t.persisted&&e(t)}),!0)},l="function"==typeof WeakSet?new WeakSet:new Set,f=function(e,t,n){var i;return function(){t.value>=0&&(n||l.has(t)||"hidden"===document.visibilityState)&&(t.delta=t.value-(i||0),(t.delta||void 0===i)&&(i=t.value,e(t)))}},w=-1,v=function(){return"hidden"===document.visibilityState?0:1/0},m=function(){u((function(e){var t=e.timeStamp;w=t}),!0)},p=function(){return w<0&&(w=v(),m(),d((function(){setTimeout((function(){w=v(),m()}),0)}))),{get timeStamp(){return w}}},g={passive:!0,capture:!0},b=new Date,y=function(e,t){i||(i=t,a=e,o=new Date,A(removeEventListener),h())},h=function(){if(a>=0&&a<o-b){var e={entryType:"first-input",name:i.type,target:i.target,cancelable:i.cancelable,startTime:i.timeStamp,processingStart:i.timeStamp+a};r.forEach((function(t){t(e)})),r=[]}},D=function(e){if(e.cancelable){var t=(e.timeStamp>1e12?new Date:performance.now())-e.timeStamp;"pointerdown"==e.type?function(e,t){var n=function(){y(e,t),a()},i=function(){a()},a=function(){removeEventListener("pointerup",n,g),removeEventListener("pointercancel",i,g)};addEventListener("pointerup",n,g),addEventListener("pointercancel",i,g)}(t,e):y(t,e)}},A=function(e){["mousedown","keydown","touchstart","pointerdown"].forEach((function(t){return e(t,D,g)}))};const V={CLS:[.1,.25],FCP:[1800,3e3],FID:[100,300],LCP:[2500,4e3]},S=window.webVitalsAnalyticsData.measurementVersion?window.webVitalsAnalyticsData.measurementVersion:"dimension1",_=window.webVitalsAnalyticsData.clientId?window.webVitalsAnalyticsData.cliegantId:"dimension2",L=window.webVitalsAnalyticsData.segments?window.webVitalsAnalyticsData.segments:"dimension3",E=window.webVitalsAnalyticsData.config?window.webVitalsAnalyticsData.config:"dimension4",C=window.webVitalsAnalyticsData.eventMeta?window.webVitalsAnalyticsData.eventMeta:"dimension5",P=window.webVitalsAnalyticsData.eventDebug?window.webVitalsAnalyticsData.eventDebug:"dimension6",j=e=>{const t={page_path:location.pathname};return"gtag"===window.webVitalsAnalyticsData.delivery&&Object.assign(t,{transport_type:"beacon",measurement_version:"6"}),e.startsWith("UA-")&&("gtag"===window.webVitalsAnalyticsData.delivery&&Object.assign(t,{custom_map:{[S]:"measurement_version",[_]:"client_id",[L]:"segments",[E]:"config",[C]:"event_meta",[P]:"event_debug",metric1:"report_size"}}),"ga"===window.webVitalsAnalyticsData.delivery&&Object.assign(t,{[S]:"6"})),["config",e,t]};function k(e,t){return e>t[1]?"poor":e>t[0]?"ni":"good"}function O(e){try{let t=e.nodeName.toLowerCase();return"body"===t?"html>body":e.id?`${t}#${e.id}`:(e.className&&e.className.length&&(t+="."+[...e.classList.values()].join(".")),`${O(e.parentElement)}>${t}`)}catch(e){return"(error)"}}function F(e,t=[]){const n=t[0],i=t[t.length-1];switch(e){case"LCP":if(i)return O(i.element);break;case"FID":if(n){const{name:e}=n;return`${e}(${O(n.target)})`}break;case"CLS":if(t.length){const e=t.reduce((e,t)=>e&&e.value>t.value?e:t);if(e&&e.sources){const t=e.sources.reduce((e,t)=>e.node&&e.previousRect.width*e.previousRect.height>t.previousRect.width*t.previousRect.height?e:t);if(t)return O(t.node)}}break;default:return"(not set)"}}function T({name:e,value:t,delta:n,id:i,entries:a}){void 0!==window.webVitalsAnalyticsData.gtag_id&&gtag("event",e,{event_category:"Web Vitals",event_label:i,value:Math.round("CLS"===e?1e3*n:n),non_interaction:!0,event_meta:k(t,V[e]),metric_rating:k(t,V[e]),event_debug:F(e,a)}),void 0!==window.webVitalsAnalyticsData.ga_id&&ga("send","event",{eventCategory:"Web Vitals",eventAction:e,eventLabel:i,eventValue:Math.round("CLS"===e?1e3*n:n),nonInteraction:!0,transport:"beacon",[C]:k(t,V[e]),[P]:F(e,a)})}function M(){!function(e,t){var n,i=s("CLS",0),a=function(e){e.hadRecentInput||(i.value+=e.value,i.entries.push(e),n())},o=c("layout-shift",a);o&&(n=f(e,i,t),u((function(){o.takeRecords().map(a),n()})),d((function(){i=s("CLS",0),n=f(e,i,t)})))}(T),function(e,t){var n,i=p(),a=s("FCP"),o=c("paint",(function(e){"first-contentful-paint"===e.name&&(o&&o.disconnect(),e.startTime<i.timeStamp&&(a.value=e.startTime,a.entries.push(e),l.add(a),n()))}));o&&(n=f(e,a,t),d((function(i){a=s("FCP"),n=f(e,a,t),requestAnimationFrame((function(){requestAnimationFrame((function(){a.value=performance.now()-i.timeStamp,l.add(a),n()}))}))})))}(T),function(e,t){var n,o=p(),w=s("FID"),v=function(e){e.startTime<o.timeStamp&&(w.value=e.processingStart-e.startTime,w.entries.push(e),l.add(w),n())},m=c("first-input",v);n=f(e,w,t),m&&u((function(){m.takeRecords().map(v),m.disconnect()}),!0),m&&d((function(){var o;w=s("FID"),n=f(e,w,t),r=[],a=-1,i=null,A(addEventListener),o=v,r.push(o),h()}))}(T),function(e,t){var n,i=p(),a=s("LCP"),o=function(e){var t=e.startTime;t<i.timeStamp&&(a.value=t,a.entries.push(e)),n()},r=c("largest-contentful-paint",o);if(r){n=f(e,a,t);var w=function(){l.has(a)||(r.takeRecords().map(o),r.disconnect(),l.add(a),n())};["keydown","click"].forEach((function(e){addEventListener(e,w,{once:!0,capture:!0})})),u(w,!0),d((function(i){a=s("LCP"),n=f(e,a,t),requestAnimationFrame((function(){requestAnimationFrame((function(){a.value=performance.now()-i.timeStamp,l.add(a),n()}))}))}))}}(T)}function I(){if(void 0===window.webVitalsAnalyticsData)return!1;void 0!==window.webVitalsAnalyticsData.gtag_id?window.webVitalsAnalyticsData.delivery="gtag":void 0!==window.webVitalsAnalyticsData.ga_id&&(window.webVitalsAnalyticsData.delivery="ga"),"gtag"===window.webVitalsAnalyticsData.delivery&&(window.webVitalsAnalyticsData.type="gtag",void 0===window.gtag&&(window.gtag=console.log),gtag("js",new Date),gtag(...j(window.webVitalsAnalyticsData.gtag_id))),"ga"===window.webVitalsAnalyticsData.delivery&&(void 0===window.ga?window.ga=console.log:(ga("js",new Date),ga(...j(window.webVitalsAnalyticsData.ga_id)))),M()}requestIdleCallback(I)}]);