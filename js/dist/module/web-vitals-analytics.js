(()=>{"use strict";const e={CLS:[.1,.25],FCP:[1800,3e3],FID:[100,300],LCP:[2500,4e3]},t=window.webVitalsAnalyticsData.measurementVersion?window.webVitalsAnalyticsData.measurementVersion:"dimension1",n=window.webVitalsAnalyticsData.eventMeta?window.webVitalsAnalyticsData.eventMeta:"dimension2",i=window.webVitalsAnalyticsData.eventDebug?window.webVitalsAnalyticsData.eventDebug:"dimension3";function a(e){return window[e]||console.log}function r(e,t){return e>t[1]?"poor":e>t[0]?"ni":"good"}function o(e){try{let t=e.nodeName.toLowerCase();return"body"===t?"html>body":e.id?`${t}#${e.id}`:(e.className&&e.className.length&&(t+=`.${[...e.classList.values()].join(".")}`),`${o(e.parentElement)}>${t}`)}catch(e){return"(error)"}}function c(e,t=[]){const n=t[0],i=t[t.length-1];switch(e){case"LCP":if(i)return o(i.element);break;case"FID":if(n){const{name:e}=n;return`${e}(${o(n.target)})`}break;case"CLS":if(t.length){const e=t.reduce(((e,t)=>e&&e.value>t.value?e:t));if(e&&e.sources){const t=e.sources.reduce(((e,t)=>e.node&&e.previousRect.width*e.previousRect.height>t.previousRect.width*t.previousRect.height?e:t));if(t)return o(t.node)}}break;default:return"(not set)"}}function u({name:o,value:u,delta:s,id:d,entries:f}){let l=!1;const m=window.webVitalsAnalyticsData[0];m.gtag_id&&(l||(!function(e){"gtag"in window&&gtag("config",e,{transport_type:"beacon",measurement_version:"6",custom_map:{[t]:"measurement_version",[n]:"event_meta",[i]:"event_debug"}})}(m.gtag_id),l=!0),a("gtag")("event",o,{event_category:"Web Vitals",event_label:d,value:Math.round("CLS"===o?1e3*s:s),non_interaction:!0,event_meta:r(u,e[o]),event_debug:c(o,f)})),m.ga_id&&a("ga")("send","event",{eventCategory:"Web Vitals",eventAction:o,eventLabel:d,eventValue:Math.round("CLS"===o?1e3*s:s),nonInteraction:!0,transport:"beacon",[n]:r(u,e[o]),[i]:c(o,f),[t]:"6"}),m.ga4_id&&a("gtag")("event",o,{value:s,metric_id:d,metric_value:Math.round("CLS"===o?1e3*s:s),event_meta:r(u,e[o]),event_debug:c(o,f),measurement_version:"6"})}var s,d,f,l,m=function(e,t){return{name:e,value:void 0===t?-1:t,delta:0,entries:[],id:"v2-".concat(Date.now(),"-").concat(Math.floor(8999999999999*Math.random())+1e12)}},v=function(e,t){try{if(PerformanceObserver.supportedEntryTypes.includes(e)){if("first-input"===e&&!("PerformanceEventTiming"in self))return;var n=new PerformanceObserver((function(e){return e.getEntries().map(t)}));return n.observe({type:e,buffered:!0}),n}}catch(e){}},p=function(e,t){var n=function n(i){"pagehide"!==i.type&&"hidden"!==document.visibilityState||(e(i),t&&(removeEventListener("visibilitychange",n,!0),removeEventListener("pagehide",n,!0)))};addEventListener("visibilitychange",n,!0),addEventListener("pagehide",n,!0)},w=function(e){addEventListener("pageshow",(function(t){t.persisted&&e(t)}),!0)},g=function(e,t,n){var i;return function(a){t.value>=0&&(a||n)&&(t.delta=t.value-(i||0),(t.delta||void 0===i)&&(i=t.value,e(t)))}},h=-1,b=function(){return"hidden"===document.visibilityState?0:1/0},y=function(){p((function(e){var t=e.timeStamp;h=t}),!0)},L=function(){return h<0&&(h=b(),y(),w((function(){setTimeout((function(){h=b(),y()}),0)}))),{get firstHiddenTime(){return h}}},E=function(e,t){var n,i=L(),a=m("FCP"),r=function(e){"first-contentful-paint"===e.name&&(c&&c.disconnect(),e.startTime<i.firstHiddenTime&&(a.value=e.startTime,a.entries.push(e),n(!0)))},o=performance.getEntriesByName&&performance.getEntriesByName("first-contentful-paint")[0],c=o?null:v("paint",r);(o||c)&&(n=g(e,a,t),o&&r(o),w((function(i){a=m("FCP"),n=g(e,a,t),requestAnimationFrame((function(){requestAnimationFrame((function(){a.value=performance.now()-i.timeStamp,n(!0)}))}))})))},_=!1,S=-1,C={passive:!0,capture:!0},D=new Date,T=function(e,t){s||(s=t,d=e,f=new Date,k(removeEventListener),A())},A=function(){if(d>=0&&d<f-D){var e={entryType:"first-input",name:s.type,target:s.target,cancelable:s.cancelable,startTime:s.timeStamp,processingStart:s.timeStamp+d};l.forEach((function(t){t(e)})),l=[]}},V=function(e){if(e.cancelable){var t=(e.timeStamp>1e12?new Date:performance.now())-e.timeStamp;"pointerdown"==e.type?function(e,t){var n=function(){T(e,t),a()},i=function(){a()},a=function(){removeEventListener("pointerup",n,C),removeEventListener("pointercancel",i,C)};addEventListener("pointerup",n,C),addEventListener("pointercancel",i,C)}(t,e):T(t,e)}},k=function(e){["mousedown","keydown","touchstart","pointerdown"].forEach((function(t){return e(t,V,C)}))},F=new Set;function P(){!function(e,t){_||(E((function(e){S=e.value})),_=!0);var n,i=function(t){S>-1&&e(t)},a=m("CLS",0),r=0,o=[],c=function(e){if(!e.hadRecentInput){var t=o[0],i=o[o.length-1];r&&e.startTime-i.startTime<1e3&&e.startTime-t.startTime<5e3?(r+=e.value,o.push(e)):(r=e.value,o=[e]),r>a.value&&(a.value=r,a.entries=o,n())}},u=v("layout-shift",c);u&&(n=g(i,a,t),p((function(){u.takeRecords().map(c),n(!0)})),w((function(){r=0,S=-1,a=m("CLS",0),n=g(i,a,t)})))}(u),E(u),function(e,t){var n,i=L(),a=m("FID"),r=function(e){e.startTime<i.firstHiddenTime&&(a.value=e.processingStart-e.startTime,a.entries.push(e),n(!0))},o=v("first-input",r);n=g(e,a,t),o&&p((function(){o.takeRecords().map(r),o.disconnect()}),!0),o&&w((function(){var i;a=m("FID"),n=g(e,a,t),l=[],d=-1,s=null,k(addEventListener),i=r,l.push(i),A()}))}(u),function(e,t){var n,i=L(),a=m("LCP"),r=function(e){var t=e.startTime;t<i.firstHiddenTime&&(a.value=t,a.entries.push(e)),n()},o=v("largest-contentful-paint",r);if(o){n=g(e,a,t);var c=function(){F.has(a.id)||(o.takeRecords().map(r),o.disconnect(),F.add(a.id),n(!0))};["keydown","click"].forEach((function(e){addEventListener(e,c,{once:!0,capture:!0})})),p(c,!0),w((function(i){a=m("LCP"),n=g(e,a,t),requestAnimationFrame((function(){requestAnimationFrame((function(){a.value=performance.now()-i.timeStamp,F.add(a.id),n(!0)}))}))}))}}(u)}"requestIdleCallback"in window&&"object"==typeof window.webVitalsAnalyticsData&&window.requestIdleCallback(P)})();