(()=>{"use strict";function t({name:t,value:e,delta:n,id:r,attribution:i,rating:a}){const o=window.webVitalsAnalyticsData[0],c={value:n,metric_id:r,metric_value:e,metric_delta:Math.round("CLS"===t?1e3*n:n),metric_rating:a};switch(t){case"CLS":c.debug_target=i.largestShiftTarget;break;case"INP":const{processingDuration:t,presentationDelay:e,interactionTarget:n,interactionType:r}=i,a=i.longAnimationFrameEntries.at(-1),o=a?.scripts?.sort(((t,e)=>e.duration-t.duration))[0];if(c.processingDuration=Math.round(t),c.presentationDelay=Math.round(e),c.debug_target=n,c.interactionType=r,o){const{invokerType:t,invoker:e,sourceURL:n,sourceCharPosition:r,sourceFunctionName:i}=o,{startTime:u,duration:s,styleAndLayoutStart:f}=a,d=u+s-f;c.invokerType=t,c.invoker=e,c.sourceURL=n,c.sourceCharPosition=r,c.sourceFunctionName=i,c.styleLayoutDuration=d}break;case"LCP":c.debug_target=i.element;break;default:return"(not set)"}o&&o.ga4_id&&(window["gtag"]||console.log)("event",t,c)}var e,n,r=function(){var t=self.performance&&performance.getEntriesByType&&performance.getEntriesByType("navigation")[0];if(t&&t.responseStart>0&&t.responseStart<performance.now())return t},i=function(t){if("loading"===document.readyState)return"loading";var e=r();if(e){if(t<e.domInteractive)return"loading";if(0===e.domContentLoadedEventStart||t<e.domContentLoadedEventStart)return"dom-interactive";if(0===e.domComplete||t<e.domComplete)return"dom-content-loaded"}return"complete"},a=function(t){var e=t.nodeName;return 1===t.nodeType?e.toLowerCase():e.toUpperCase().replace(/^#/,"")},o=function(t,e){var n="";try{for(;t&&9!==t.nodeType;){var r=t,i=r.id?"#"+r.id:a(r)+(r.classList&&r.classList.value&&r.classList.value.trim()&&r.classList.value.trim().length?"."+r.classList.value.trim().replace(/\s+/g,"."):"");if(n.length+i.length>(e||100)-1)return n||i;if(n=n?i+">"+n:i,r.id)break;t=r.parentNode}}catch(t){}return n},c=-1,u=function(){return c},s=function(t){addEventListener("pageshow",(function(e){e.persisted&&(c=e.timeStamp,t(e))}),!0)},f=function(){var t=r();return t&&t.activationStart||0},d=function(t,e){var n=r(),i="navigate";return u()>=0?i="back-forward-cache":n&&(document.prerendering||f()>0?i="prerender":document.wasDiscarded?i="restore":n.type&&(i=n.type.replace(/_/g,"-"))),{name:t,value:void 0===e?-1:e,rating:"good",delta:0,entries:[],id:"v4-".concat(Date.now(),"-").concat(Math.floor(8999999999999*Math.random())+1e12),navigationType:i}},l=function(t,e,n){try{if(PerformanceObserver.supportedEntryTypes.includes(t)){var r=new PerformanceObserver((function(t){Promise.resolve().then((function(){e(t.getEntries())}))}));return r.observe(Object.assign({type:t,buffered:!0},n||{})),r}}catch(t){}},m=function(t,e,n,r){var i,a;return function(o){e.value>=0&&(o||r)&&((a=e.value-(i||0))||void 0===i)&&(i=e.value,e.delta=a,e.rating=function(t,e){return t>e[1]?"poor":t>e[0]?"needs-improvement":"good"}(e.value,n),t(e))}},p=function(t){requestAnimationFrame((function(){return requestAnimationFrame((function(){return t()}))}))},v=function(t){document.addEventListener("visibilitychange",(function(){"hidden"===document.visibilityState&&t()}))},g=function(t){var e=!1;return function(){e||(t(),e=!0)}},h=-1,y=function(){return"hidden"!==document.visibilityState||document.prerendering?1/0:0},T=function(t){"hidden"===document.visibilityState&&h>-1&&(h="visibilitychange"===t.type?t.timeStamp:0,S())},E=function(){addEventListener("visibilitychange",T,!0),addEventListener("prerenderingchange",T,!0)},S=function(){removeEventListener("visibilitychange",T,!0),removeEventListener("prerenderingchange",T,!0)},b=function(){return h<0&&(h=y(),E(),s((function(){setTimeout((function(){h=y(),E()}),0)}))),{get firstHiddenTime(){return h}}},C=function(t){document.prerendering?addEventListener("prerenderingchange",(function(){return t()}),!0):t()},w=[1800,3e3],L=function(t,e){e=e||{},C((function(){var n,r=b(),i=d("FCP"),a=l("paint",(function(t){t.forEach((function(t){"first-contentful-paint"===t.name&&(a.disconnect(),t.startTime<r.firstHiddenTime&&(i.value=Math.max(t.startTime-f(),0),i.entries.push(t),n(!0)))}))}));a&&(n=m(t,i,w,e.reportAllChanges),s((function(r){i=d("FCP"),n=m(t,i,w,e.reportAllChanges),p((function(){i.value=performance.now()-r.timeStamp,n(!0)}))})))}))},M=[.1,.25],D=0,k=1/0,I=0,A=function(t){t.forEach((function(t){t.interactionId&&(k=Math.min(k,t.interactionId),I=Math.max(I,t.interactionId),D=I?(I-k)/7+1:0)}))},P=function(){"interactionCount"in performance||e||(e=l("event",A,{type:"event",buffered:!0,durationThreshold:0}))},x=[],F=new Map,B=0,R=function(){return(e?D:performance.interactionCount||0)-B},_=[],q=function(t){if(_.forEach((function(e){return e(t)})),t.interactionId||"first-input"===t.entryType){var e=x[x.length-1],n=F.get(t.interactionId);if(n||x.length<10||t.duration>e.latency){if(n)t.duration>n.latency?(n.entries=[t],n.latency=t.duration):t.duration===n.latency&&t.startTime===n.entries[0].startTime&&n.entries.push(t);else{var r={id:t.interactionId,latency:t.duration,entries:[t]};F.set(r.id,r),x.push(r)}x.sort((function(t,e){return e.latency-t.latency})),x.length>10&&x.splice(10).forEach((function(t){return F.delete(t.id)}))}}},O=function(t){var e=self.requestIdleCallback||self.setTimeout,n=-1;return t=g(t),"hidden"===document.visibilityState?t():(n=e(t),v(t)),n},j=[200,500],N=[],H=new Map,U=[],V=new WeakMap,W=new Map,z=-1,G=function(t){t.forEach((function(t){return N.push(t)}))},J=function(){W.size>10&&W.forEach((function(t,e){F.has(e)||W.delete(e)})),U=U.slice(-50);var t=new Set(U.concat(x.map((function(t){return V.get(t.entries[0])}))));H.forEach((function(e,n){t.has(n)||H.delete(n)}));var e=new Set;H.forEach((function(t){K(t.startTime,t.processingEnd).forEach((function(t){e.add(t)}))})),N=Array.from(e),z=-1};_.push((function(t){t.interactionId&&t.target&&!W.has(t.interactionId)&&W.set(t.interactionId,t.target)}),(function(t){for(var e,n=t.startTime+t.duration,r=U.length-1;r>=0;r--)if(e=U[r],Math.abs(n-e)<=8){var i=H.get(e);i.startTime=Math.min(t.startTime,i.startTime),i.processingStart=Math.min(t.processingStart,i.processingStart),i.processingEnd=Math.max(t.processingEnd,i.processingEnd),i.entries.push(t),n=e;break}n!==e&&(U.push(n),H.set(n,{startTime:t.startTime,processingStart:t.processingStart,processingEnd:t.processingEnd,entries:[t]})),(t.interactionId||"first-input"===t.entryType)&&V.set(t,n)}),(function(){z<0&&(z=O(J))}));var K=function(t,e){for(var n,r=[],i=0;n=N[i];i++)if(!(n.startTime+n.duration<t)){if(n.startTime>e)break;r.push(n)}return r},Q=function(t,e){n||(n=l("long-animation-frame",G)),function(t,e){e=e||{},C((function(){var n;P();var r,i=d("INP"),a=function(t){t.forEach(q);var e,n=(e=Math.min(x.length-1,Math.floor(R()/50)),x[e]);n&&n.latency!==i.value&&(i.value=n.latency,i.entries=n.entries,r())},o=l("event",a,{durationThreshold:null!==(n=e.durationThreshold)&&void 0!==n?n:40});r=m(t,i,j,e.reportAllChanges),o&&("PerformanceEventTiming"in self&&"interactionId"in PerformanceEventTiming.prototype&&o.observe({type:"first-input",buffered:!0}),v((function(){a(o.takeRecords()),r(!0)})),s((function(){B=0,x.length=0,F.clear(),i=d("INP"),r=m(t,i,j,e.reportAllChanges)})))}))}((function(e){O((function(){var n=function(t){var e=t.entries[0],n=V.get(e),r=H.get(n),a=e.processingStart,c=r.processingEnd,u=r.entries.sort((function(t,e){return t.processingStart-e.processingStart})),s=K(e.startTime,c),f=t.entries.find((function(t){return t.target})),d=f&&f.target||W.get(e.interactionId),l=[e.startTime+e.duration,c].concat(s.map((function(t){return t.startTime+t.duration}))),m=Math.max.apply(Math,l),p={interactionTarget:o(d),interactionTargetElement:d,interactionType:e.name.startsWith("key")?"keyboard":"pointer",interactionTime:e.startTime,nextPaintTime:m,processedEventEntries:u,longAnimationFrameEntries:s,inputDelay:a-e.startTime,processingDuration:c-a,presentationDelay:Math.max(m-c,0),loadState:i(e.startTime)};return Object.assign(t,{attribution:p})}(e);t(n)}))}),e)},X=[2500,4e3],Y={},Z=[800,1800],$=function t(e){document.prerendering?C((function(){return t(e)})):"complete"!==document.readyState?addEventListener("load",(function(){return t(e)}),!0):setTimeout(e,0)},tt=function(t,e){e=e||{};var n=d("TTFB"),i=m(t,n,Z,e.reportAllChanges);$((function(){var a=r();a&&(n.value=Math.max(a.responseStart-f(),0),n.entries=[a],i(!0),s((function(){n=d("TTFB",0),(i=m(t,n,Z,e.reportAllChanges))(!0)})))}))};new Date;function et(){!function(t,e){!function(t,e){e=e||{},L(g((function(){var n,r=d("CLS",0),i=0,a=[],o=function(t){t.forEach((function(t){if(!t.hadRecentInput){var e=a[0],n=a[a.length-1];i&&t.startTime-n.startTime<1e3&&t.startTime-e.startTime<5e3?(i+=t.value,a.push(t)):(i=t.value,a=[t])}})),i>r.value&&(r.value=i,r.entries=a,n())},c=l("layout-shift",o);c&&(n=m(t,r,M,e.reportAllChanges),v((function(){o(c.takeRecords()),n(!0)})),s((function(){i=0,r=d("CLS",0),n=m(t,r,M,e.reportAllChanges),p((function(){return n()}))})),setTimeout(n,0))})))}((function(e){var n=function(t){var e,n={};if(t.entries.length){var r=t.entries.reduce((function(t,e){return t&&t.value>e.value?t:e}));if(r&&r.sources&&r.sources.length){var a=(e=r.sources).find((function(t){return t.node&&1===t.node.nodeType}))||e[0];a&&(n={largestShiftTarget:o(a.node),largestShiftTime:r.startTime,largestShiftValue:r.value,largestShiftSource:a,largestShiftEntry:r,loadState:i(r.startTime)})}}return Object.assign(t,{attribution:n})}(e);t(n)}),e)}(t),function(t,e){L((function(e){var n=function(t){var e={timeToFirstByte:0,firstByteToFCP:t.value,loadState:i(u())};if(t.entries.length){var n=r(),a=t.entries[t.entries.length-1];if(n){var o=n.activationStart||0,c=Math.max(0,n.responseStart-o);e={timeToFirstByte:c,firstByteToFCP:t.value-c,loadState:i(t.entries[0].startTime),navigationEntry:n,fcpEntry:a}}}return Object.assign(t,{attribution:e})}(e);t(n)}),e)}(t),function(t,e){!function(t,e){e=e||{},C((function(){var n,r=b(),i=d("LCP"),a=function(t){e.reportAllChanges||(t=t.slice(-1)),t.forEach((function(t){t.startTime<r.firstHiddenTime&&(i.value=Math.max(t.startTime-f(),0),i.entries=[t],n())}))},o=l("largest-contentful-paint",a);if(o){n=m(t,i,X,e.reportAllChanges);var c=g((function(){Y[i.id]||(a(o.takeRecords()),o.disconnect(),Y[i.id]=!0,n(!0))}));["keydown","click"].forEach((function(t){addEventListener(t,(function(){return O(c)}),!0)})),v(c),s((function(r){i=d("LCP"),n=m(t,i,X,e.reportAllChanges),p((function(){i.value=performance.now()-r.timeStamp,Y[i.id]=!0,n(!0)}))}))}}))}((function(e){var n=function(t){var e={timeToFirstByte:0,resourceLoadDelay:0,resourceLoadDuration:0,elementRenderDelay:t.value};if(t.entries.length){var n=r();if(n){var i=n.activationStart||0,a=t.entries[t.entries.length-1],c=a.url&&performance.getEntriesByType("resource").filter((function(t){return t.name===a.url}))[0],u=Math.max(0,n.responseStart-i),s=Math.max(u,c?(c.requestStart||c.startTime)-i:0),f=Math.max(s,c?c.responseEnd-i:0),d=Math.max(f,a.startTime-i);e={element:o(a.element),timeToFirstByte:u,resourceLoadDelay:s-u,resourceLoadDuration:f-s,elementRenderDelay:d-f,navigationEntry:n,lcpEntry:a},a.url&&(e.url=a.url),c&&(e.lcpResourceEntry=c)}}return Object.assign(t,{attribution:e})}(e);t(n)}),e)}(t),function(t,e){tt((function(e){var n=function(t){var e={waitingDuration:0,cacheDuration:0,dnsDuration:0,connectionDuration:0,requestDuration:0};if(t.entries.length){var n=t.entries[0],r=n.activationStart||0,i=Math.max((n.workerStart||n.fetchStart)-r,0),a=Math.max(n.domainLookupStart-r,0),o=Math.max(n.connectStart-r,0),c=Math.max(n.connectEnd-r,0);e={waitingDuration:i,cacheDuration:a-i,dnsDuration:o-a,connectionDuration:c-o,requestDuration:t.value-c,navigationEntry:n}}return Object.assign(t,{attribution:e})}(e);t(n)}),e)}(t),Q(t)}"requestIdleCallback"in window&&"object"==typeof window.webVitalsAnalyticsData&&window.requestIdleCallback(et)})();