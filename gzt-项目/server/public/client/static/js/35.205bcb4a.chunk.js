(window.webpackJsonp=window.webpackJsonp||[]).push([[35,38],{459:function(e,t,a){"use strict";a(24),a(463)},460:function(e,t,a){"use strict";var n=a(643);t.a=n.a},461:function(e,t,a){"use strict";a(24),a(463)},462:function(e,t,a){"use strict";var n=a(642);t.a=n.a},481:function(e,t,a){"use strict";a.d(t,"a",function(){return c});var n=a(160),r=a(0),o=a.n(r);function c(e){var t=e.text,a=e.colon,r=void 0===a||a,c=Object(n.a)(e,["text","colon"]);return o.a.createElement("span",Object.assign({className:"text-label"},c),t,r?"\uff1a":"")}},522:function(e,t,a){"use strict";var n=a(0),r=a(25),o=a(158),c=a(13),l=a(27),i=a(68),s=a(70),p=a(11);function u(e){return(u="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"===typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function m(){return(m=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}function d(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function f(e){return(f=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function v(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function b(e,t){return(b=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}var y=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},h=function(e){function t(e){var a,r,c;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),r=this,c=f(t).call(this,e),(a=!c||"object"!==u(c)&&"function"!==typeof c?v(r):c).onConfirm=function(e){a.setVisible(!1,e);var t=a.props.onConfirm;t&&t.call(v(a),e)},a.onCancel=function(e){a.setVisible(!1,e);var t=a.props.onCancel;t&&t.call(v(a),e)},a.onVisibleChange=function(e){a.setVisible(e)},a.saveTooltip=function(e){a.tooltip=e},a.renderOverlay=function(e,t){var r=a.props,o=r.okButtonProps,c=r.cancelButtonProps,i=r.title,s=r.cancelText,p=r.okText,u=r.okType,d=r.icon;return n.createElement("div",null,n.createElement("div",{className:"".concat(e,"-inner-content")},n.createElement("div",{className:"".concat(e,"-message")},d,n.createElement("div",{className:"".concat(e,"-message-title")},i)),n.createElement("div",{className:"".concat(e,"-buttons")},n.createElement(l.a,m({onClick:a.onCancel,size:"small"},c),s||t.cancelText),n.createElement(l.a,m({onClick:a.onConfirm,type:u,size:"small"},o),p||t.okText))))},a.renderConfirm=function(e){var t=e.getPrefixCls,r=a.props,c=r.prefixCls,l=r.placement,p=y(r,["prefixCls","placement"]),u=t("popover",c),d=n.createElement(i.a,{componentName:"Popconfirm",defaultLocale:s.a.Popconfirm},function(e){return a.renderOverlay(u,e)});return n.createElement(o.a,m({},p,{prefixCls:u,placement:l,onVisibleChange:a.onVisibleChange,visible:a.state.visible,overlay:d,ref:a.saveTooltip}))},a.state={visible:e.visible},a}var a,r,c;return function(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&b(e,t)}(t,n["Component"]),a=t,c=[{key:"getDerivedStateFromProps",value:function(e){return"visible"in e?{visible:e.visible}:"defaultVisible"in e?{visible:e.defaultVisible}:null}}],(r=[{key:"getPopupDomNode",value:function(){return this.tooltip.getPopupDomNode()}},{key:"setVisible",value:function(e,t){var a=this.props;"visible"in a||this.setState({visible:e});var n=a.onVisibleChange;n&&n(e,t)}},{key:"render",value:function(){return n.createElement(p.a,null,this.renderConfirm)}}])&&d(a.prototype,r),c&&d(a,c),t}();h.defaultProps={transitionName:"zoom-big",placement:"top",trigger:"click",okType:"primary",icon:n.createElement(c.a,{type:"exclamation-circle",theme:"filled"})},Object(r.polyfill)(h),t.a=h},524:function(e,t,a){"use strict";a(24),a(554),a(120)},554:function(e,t,a){},603:function(e,t,a){"use strict";a(24),a(628),a(550),a(461),a(459)},604:function(e,t,a){"use strict";var n=a(0),r=a(6),o=a.n(r),c=a(32),l=a(11);function i(){return(i=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var s=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},p=function(e){return n.createElement(l.a,null,function(t){var a=t.getPrefixCls,r=e.prefixCls,c=e.className,l=s(e,["prefixCls","className"]),p=a("card",r),u=o()("".concat(p,"-grid"),c);return n.createElement("div",i({},l,{className:u}))})};function u(){return(u=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var m=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},d=function(e){return n.createElement(l.a,null,function(t){var a=t.getPrefixCls,r=e.prefixCls,c=e.className,l=e.avatar,i=e.title,s=e.description,p=m(e,["prefixCls","className","avatar","title","description"]),d=a("card",r),f=o()("".concat(d,"-meta"),c),v=l?n.createElement("div",{className:"".concat(d,"-meta-avatar")},l):null,b=i?n.createElement("div",{className:"".concat(d,"-meta-title")},i):null,y=s?n.createElement("div",{className:"".concat(d,"-meta-description")},s):null,h=b||y?n.createElement("div",{className:"".concat(d,"-meta-detail")},b,y):null;return n.createElement("div",u({},p,{className:f}),v,h)})},f=a(552),v=a(462),b=a(460),y=a(21);function h(e){return(h="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"===typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function g(){return(g=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}function E(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function O(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function C(e,t){return!t||"object"!==h(t)&&"function"!==typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function w(e){return(w=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function T(e,t){return(T=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}a.d(t,"a",function(){return k});var x=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},k=function(e){function t(){var e;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),(e=C(this,w(t).apply(this,arguments))).onTabChange=function(t){e.props.onTabChange&&e.props.onTabChange(t)},e.renderCard=function(t){var a,r,l=t.getPrefixCls,i=e.props,s=i.prefixCls,p=i.className,u=i.extra,m=i.headStyle,d=void 0===m?{}:m,y=i.bodyStyle,h=void 0===y?{}:y,O=(i.noHovering,i.hoverable,i.title),C=i.loading,w=i.bordered,T=void 0===w||w,k=i.size,G=void 0===k?"default":k,_=i.type,j=i.cover,N=i.actions,S=i.tabList,P=i.children,V=i.activeTabKey,M=i.defaultActiveTabKey,q=x(i,["prefixCls","className","extra","headStyle","bodyStyle","noHovering","hoverable","title","loading","bordered","size","type","cover","actions","tabList","children","activeTabKey","defaultActiveTabKey"]),H=l("card",s),R=o()(H,p,(E(a={},"".concat(H,"-loading"),C),E(a,"".concat(H,"-bordered"),T),E(a,"".concat(H,"-hoverable"),e.getCompatibleHoverable()),E(a,"".concat(H,"-contain-grid"),e.isContainGrid()),E(a,"".concat(H,"-contain-tabs"),S&&S.length),E(a,"".concat(H,"-").concat(G),"default"!==G),E(a,"".concat(H,"-type-").concat(_),!!_),a)),D=0===h.padding||"0px"===h.padding?{padding:24}:void 0,L=n.createElement("div",{className:"".concat(H,"-loading-content"),style:D},n.createElement(v.a,{gutter:8},n.createElement(b.a,{span:22},n.createElement("div",{className:"".concat(H,"-loading-block")}))),n.createElement(v.a,{gutter:8},n.createElement(b.a,{span:8},n.createElement("div",{className:"".concat(H,"-loading-block")})),n.createElement(b.a,{span:15},n.createElement("div",{className:"".concat(H,"-loading-block")}))),n.createElement(v.a,{gutter:8},n.createElement(b.a,{span:6},n.createElement("div",{className:"".concat(H,"-loading-block")})),n.createElement(b.a,{span:18},n.createElement("div",{className:"".concat(H,"-loading-block")}))),n.createElement(v.a,{gutter:8},n.createElement(b.a,{span:13},n.createElement("div",{className:"".concat(H,"-loading-block")})),n.createElement(b.a,{span:9},n.createElement("div",{className:"".concat(H,"-loading-block")}))),n.createElement(v.a,{gutter:8},n.createElement(b.a,{span:4},n.createElement("div",{className:"".concat(H,"-loading-block")})),n.createElement(b.a,{span:3},n.createElement("div",{className:"".concat(H,"-loading-block")})),n.createElement(b.a,{span:16},n.createElement("div",{className:"".concat(H,"-loading-block")})))),z=void 0!==V,A=E({},z?"activeKey":"defaultActiveKey",z?V:M),I=S&&S.length?n.createElement(f.a,g({},A,{className:"".concat(H,"-head-tabs"),size:"large",onChange:e.onTabChange}),S.map(function(e){return n.createElement(f.a.TabPane,{tab:e.tab,disabled:e.disabled,key:e.key})})):null;(O||u||I)&&(r=n.createElement("div",{className:"".concat(H,"-head"),style:d},n.createElement("div",{className:"".concat(H,"-head-wrapper")},O&&n.createElement("div",{className:"".concat(H,"-head-title")},O),u&&n.createElement("div",{className:"".concat(H,"-extra")},u)),I));var B=j?n.createElement("div",{className:"".concat(H,"-cover")},j):null,K=n.createElement("div",{className:"".concat(H,"-body"),style:h},C?L:P),U=N&&N.length?n.createElement("ul",{className:"".concat(H,"-actions")},e.getAction(N)):null,J=Object(c.default)(q,["onTabChange"]);return n.createElement("div",g({},J,{className:R}),r,B,K,U)},e}var a,r,i;return function(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&T(e,t)}(t,n["Component"]),a=t,(r=[{key:"componentDidMount",value:function(){"noHovering"in this.props&&(Object(y.a)(!this.props.noHovering,"Card","`noHovering` is deprecated, you can remove it safely or use `hoverable` instead."),Object(y.a)(!!this.props.noHovering,"Card","`noHovering={false}` is deprecated, use `hoverable` instead."))}},{key:"isContainGrid",value:function(){var e;return n.Children.forEach(this.props.children,function(t){t&&t.type&&t.type===p&&(e=!0)}),e}},{key:"getAction",value:function(e){return e.map(function(t,a){return n.createElement("li",{style:{width:"".concat(100/e.length,"%")},key:"action-".concat(a)},n.createElement("span",null,t))})}},{key:"getCompatibleHoverable",value:function(){var e=this.props,t=e.noHovering,a=e.hoverable;return"noHovering"in this.props?!t||a:!!a}},{key:"render",value:function(){return n.createElement(l.a,null,this.renderCard)}}])&&O(a.prototype,r),i&&O(a,i),t}();k.Grid=p,k.Meta=d},628:function(e,t,a){},884:function(e,t,a){"use strict";a.d(t,"a",function(){return O});var n=a(2),r=a.n(n),o=(a(58),a(12)),c=(a(155),a(94)),l=(a(461),a(462)),i=(a(524),a(522)),s=(a(233),a(13)),p=(a(459),a(460)),u=(a(154),a(93)),m=a(57),d=a(153),f=(a(603),a(604)),v=a(0),b=a.n(v),y=a(89),h=a(481),g=a(3),E=f.a.Meta;function O(e){var t=e.pathname,a=e.params,n=e.type,O=e.onEnableStateChange,C=e.onGroupMove,w=e.changeEnableStateUrl,T=e.deleteUrl,x=e.onDeleteChange,k=Object(v.useState)(!1),G=Object(d.a)(k,2),_=G[0],j=G[1],N=a.allow_user_names,S=a.id,P=a.name,V=a.description,M=a.updated_at,q=a.approval_method,H=a.is_show,R=a.need_approval;function D(){return(D=Object(m.a)(r.a.mark(function e(){var t,a;return r.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return j(!0),t="export"===n?{id:S,is_show:1===H?"disable":"enable"}:{id:S},e.next=4,Object(g.a)(w,{method:"POST",body:t});case 4:a=e.sent,j(!1),"success"===a.status?(o.a.success(1===H?"\u7981\u7528\u6210\u529f":"\u542f\u7528\u6210\u529f"),O&&O()):o.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 7:case"end":return e.stop()}},e)}))).apply(this,arguments)}function L(){return(L=Object(m.a)(r.a.mark(function e(){var t;return r.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:if(T){e.next=2;break}return e.abrupt("return");case 2:return j(!0),e.next=5,Object(g.a)(T,{method:"POST",body:{id:S}});case 5:t=e.sent,j(!1),"success"===t.status?(o.a.success("\u5220\u9664\u6210\u529f"),x&&x()):o.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 8:case"end":return e.stop()}},e)}))).apply(this,arguments)}var z=1===H?"\u786e\u5b9a\u5220\u9664\u8be5\u6a21\u677f\u5417?\u5220\u9664\u540e\uff0c\u53ef\u80fd\u4f1a\u5f71\u54cd\u5230\u6b63\u5728\u8fdb\u884c\u4e2d\u7684\u8bc4\u5ba1\uff0c\u8bf7\u786e\u8ba4\u5f53\u524d\u6a21\u677f\u4e0b\u6ca1\u6709\u6b63\u5728\u8fdb\u884c\u4e2d\u7684\u8bc4\u5ba1":"\u786e\u5b9a\u5220\u9664\u8be5\u5ba1\u6279\u6d41\u7a0b\u5417?\u5220\u9664\u540e\uff0c\u53ef\u80fd\u4f1a\u5f71\u54cd\u5230\u6b63\u5728\u8fdb\u884c\u4e2d\u7684\u8bc4\u5ba1\uff0c\u8bf7\u786e\u8ba4\u5f53\u524d\u5ba1\u6279\u6d41\u7a0b\u4e0b\u6ca1\u6709\u6b63\u5728\u8fdb\u884c\u4e2d\u7684\u8bc4\u5ba1";return b.a.createElement(c.a,{spinning:_},b.a.createElement(l.a,{type:"flex",style:{padding:"0 10px",alignItems:"center",background:"#fff",borderBottom:"1px solid #ddd"}},b.a.createElement(p.a,{span:"template"===n?6:8},b.a.createElement(f.a,{bordered:!1},b.a.createElement(E,{avatar:b.a.createElement(u.a,{size:"large",shape:"square",style:{background:"#1890ff"}},P),title:P,description:V}))),b.a.createElement(p.a,{span:"template"===n?6:8},"\u66f4\u65b0\u65f6\u95f4\uff1a",b.a.createElement("span",null,M)),"template"===n?!0===R?b.a.createElement(p.a,{span:4},q):b.a.createElement(p.a,{span:4},"\u5f53\u524d\u6a21\u677f\u4e0d\u9700\u8981\u5ba1\u6279"):null,b.a.createElement(p.a,{span:4},b.a.createElement(h.a,{text:"\u53ef\u89c1\u8303\u56f4"}),b.a.createElement("span",{style:{color:"#e8a54c"}},1===N.length?N[0]:N.length>7?"".concat(N.slice(0,7),"\u7b49").concat(N.length,"\u4eba\u53ef\u89c1"):"".concat(N))),b.a.createElement(p.a,{span:4,style:{textAlign:"right"}},t&&b.a.createElement(y.a,{to:{pathname:t,state:{type:"UPDATE",data:{id:S}}}},b.a.createElement("span",{className:"primary-color cursor-pointer",style:{padding:"0 12px"}},"\u7f16\u8f91")),1!==H&&b.a.createElement(i.a,{placement:"topLeft",icon:b.a.createElement(s.a,{type:"question-circle-o",style:{color:"red"}}),title:b.a.createElement("div",{style:{maxWidth:450}},z),onConfirm:function(){return L.apply(this,arguments)},okText:"\u786e\u5b9a",cancelText:"\u53d6\u6d88"},b.a.createElement("span",{className:"primary-color cursor-pointer",style:{padding:"0 12px"}},"\u5220\u9664")),b.a.createElement("span",{onClick:function(){return D.apply(this,arguments)},className:"primary-color cursor-pointer",style:{padding:"0 12px"}},1===H?"\u7981\u7528":"\u542f\u7528"),1===H&&b.a.createElement("span",{onClick:function(){C&&C()},className:"primary-color cursor-pointer",style:{padding:"0 12px"}},"\u79fb\u52a8\u5230"))))}},885:function(e,t,a){"use strict";a.d(t,"a",function(){return i});a(461);var n=a(462),r=(a(524),a(522)),o=(a(459),a(460)),c=a(0),l=a.n(c);function i(e){var t=e.type,a=e.count,c=e.onRenameGroup,i=e.onDeleteGroup;return l.a.createElement(n.a,{type:"flex",style:{marginTop:"10px",padding:"0 8px",lineHeight:"40px",background:"#f9f9f9"}},l.a.createElement(o.a,{span:6},l.a.createElement("span",{style:{color:"#222"}},t),l.a.createElement("span",null,"\uff08",a,"\uff09")),l.a.createElement(o.a,{span:18,style:{textAlign:"right"}},l.a.createElement("span",{onClick:function(){c&&c()},className:"primary-color cursor-pointer",style:{padding:"0 12px"}},"\u91cd\u547d\u540d"),0===a?l.a.createElement(r.a,{placement:"left",title:"\u786e\u5b9a\u5220\u9664\u8be5\u5206\u7ec4\u5417\uff1f",onConfirm:function(){i&&i()},okText:"\u786e\u5b9a",cancelText:"\u53d6\u6d88"},l.a.createElement("span",{className:"primary-color cursor-pointer",style:{padding:"0 12px"}},"\u5220\u9664\u8be5\u7ec4")):null))}},886:function(e,t,a){"use strict";a.d(t,"a",function(){return i});a(603);var n=a(604),r=(a(154),a(93)),o=a(160),c=a(0),l=a(89);function i(e){var t=e.datasource,a=e.pathname,i=e.onItemClick,s=Object(o.a)(e,["datasource","pathname","onItemClick"]);return c.createElement("div",s,t&&t.map(function(e){var t=e.id,o=e.name,s=e.count,p=e.data;return 0!==s&&c.createElement("div",{key:t},c.createElement("div",{style:{marginBottom:16}},o,"\uff08",s,"\uff09"),c.createElement("div",{className:"clearfix"},p.map(function(e){var t=e.id,o=e.name,s=e.description;return c.createElement(l.a,{key:t,to:{pathname:a,state:{type:"INSERT",data:{id:t,name:o}}}},c.createElement(n.a,{size:"small",hoverable:!0,bordered:!0,style:{float:"left",margin:"0 16px 16px 0",width:220},onClick:i},c.createElement(n.a.Meta,{className:"review-tempitem",avatar:c.createElement(r.a,{shape:"square",size:42,style:{background:"#1890ff",fontSize:14}},o&&o.substr(0,2)),title:c.createElement("span",{style:{fontSize:14}},o),description:c.createElement("div",{className:"overflow-ellipsis",style:{fontSize:12}},s)})))})))}))}},958:function(e,t,a){e.exports={groupBtn:"templatemgt_groupBtn__3lt6T",create:"templatemgt_create__2OG1c",header:"templatemgt_header__1p5Ia",templateList:"templatemgt_templateList__1lch-",tempGroupItem:"templatemgt_tempGroupItem__1y17P",active:"templatemgt_active__2j7Rw"}},988:function(e,t,a){"use strict";a.r(t),a.d(t,"default",function(){return L});a(234);var n,r=a(156),o=(a(456),a(458)),c=(a(155),a(94)),l=(a(461),a(462)),i=(a(459),a(460)),s=(a(120),a(27)),p=a(2),u=a.n(p),m=(a(58),a(12)),d=a(57),f=a(42),v=a(43),b=a(45),y=a(44),h=a(46),g=a(4),E=(a(473),a(470)),O=(a(237),a(157)),C=a(0),w=a(89),T=a(884),x=a(885),k=a(51),G=a(3),_=a(886),j=a(6),N=a.n(j),S=a(86),P=a(481),V=a(958),M=a.n(V),q=O.a.Header,H=O.a.Content,R=E.a.Group,D=E.a.Button,L=Object(k.c)(function(e){return Object(g.a)({},e.Review,{loading:e.loading.effects["".concat("Review","/queryTemplates")]})},function(e){return{queryTemplates:function(){e({type:"".concat("Review","/queryTemplates")})},queryClassicTemplates:function(){e({type:"".concat("Review","/queryClassicTemplates")})},queryTemplateGroup:function(t){e({type:"".concat("Review","/queryTemplateGroup"),payload:t})}}})(n=function(e){function t(){var e,a;Object(f.a)(this,t);for(var n=arguments.length,r=new Array(n),o=0;o<n;o++)r[o]=arguments[o];return(a=Object(b.a)(this,(e=Object(y.a)(t)).call.apply(e,[this].concat(r)))).state={addGroupVisible:!1,addGroupValue:"",renameGroupVisible:!1,renameGroup:null,templateListVisible:!1,templateCreateVisible:!1,moveToOtherGroupVisible:!1,beforeMovingTemp:null,afterMovingGroup:null,templateType:"classic"},a.handleEnableStateChange=function(){a.props.queryTemplates()},a.handleDeleteChange=function(){a.props.queryTemplates()},a.showAddGroupModal=function(){a.setState({addGroupVisible:!0,addGroupValue:""})},a.addGroup=Object(d.a)(u.a.mark(function e(){return u.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return a.setState({addGroupVisible:!1}),e.next=3,Object(G.a)("/api/c_pst_add_pst_template_type",{method:"POST",body:{name:a.state.addGroupValue}});case 3:"success"===e.sent.status?(m.a.success("\u65b0\u5efa\u6210\u529f"),a.props.queryTemplates()):m.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 5:case"end":return e.stop()}},e)})),a.cancelGroup=function(){a.setState({addGroupVisible:!1})},a.addGroupChange=function(e){a.setState({addGroupValue:e.target.value})},a.handleRenameGroup=function(e,t){a.setState({renameGroupVisible:!0,renameGroup:{id:e,name:t,oldName:t,disabled:!0}})},a.renameGroup=Object(d.a)(u.a.mark(function e(){return u.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return a.setState({renameGroupVisible:!1}),e.next=3,Object(G.a)("/api/c_pst_alter_pst_template_name",{method:"POST",body:{id:a.state.renameGroup.id,name:a.state.renameGroup.name}});case 3:"success"===e.sent.status?(m.a.success("\u91cd\u547d\u540d\u6210\u529f"),a.props.queryTemplates()):m.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 5:case"end":return e.stop()}},e)})),a.cancelRenameGroup=function(){a.setState({renameGroupVisible:!1})},a.renameGroupChange=function(e){a.setState({renameGroup:{id:a.state.renameGroup.id,name:e.target.value,oldName:a.state.renameGroup.oldName,disabled:e.target.value===a.state.renameGroup.oldName}})},a.showTemplateCreateModal=function(){a.props.queryClassicTemplates(),a.setState({templateCreateVisible:!0})},a.cancelTemplateList=function(){a.setState({templateCreateVisible:!1})},a.handleDeleteGroup=function(){var e=Object(d.a)(u.a.mark(function e(t){return u.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(G.a)("/api/c_pst_delete_pst_template_name",{method:"POST",body:{id:t}});case 2:"success"===e.sent.status?(m.a.success("\u5220\u9664\u6210\u529f"),a.props.queryTemplates()):m.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 4:case"end":return e.stop()}},e)}));return function(t){return e.apply(this,arguments)}}(),a.handleGroupMove=function(e,t,n){a.props.queryTemplateGroup(),a.setState({moveToOtherGroupVisible:!0,beforeMovingTemp:{groupId:e,id:t,name:n},afterMovingGroup:null})},a.moveToOtherGroup=Object(d.a)(u.a.mark(function e(){return u.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return a.setState({moveToOtherGroupVisible:!1}),e.next=3,Object(G.a)("/api/c_pst_move_pst_template_type",{method:"POST",body:{template_id:a.state.beforeMovingTemp.id,type_id:a.state.afterMovingGroup.id}});case 3:"success"===e.sent.status?(m.a.success("\u79fb\u52a8\u6210\u529f"),a.props.queryTemplates()):m.a.error("\u670d\u52a1\u5668\u5f02\u5e38\uff0c\u8bf7\u7a0d\u540e\u518d\u8bd5");case 5:case"end":return e.stop()}},e)})),a.cancelMove=function(){a.setState({moveToOtherGroupVisible:!1})},a.handleMoveGroupChecked=function(e){Object(S.a)(a.state.beforeMovingTemp.groupId)!==Object(S.a)(e)&&a.setState({afterMovingGroup:{id:e}})},a.handleTempTypeChange=function(e){"classicTemplate"===e.target.value?a.setState({templateType:"classic"}):a.setState({templateType:"exist"})},a}return Object(h.a)(t,e),Object(v.a)(t,[{key:"componentDidMount",value:function(){this.props.queryTemplates()}},{key:"render",value:function(){var e=this,t=this.props,a=t.classicTemplates,n=t.templates,p=t.loading,u=t.templateGroup,m=this.state,d=m.addGroupVisible,f=m.addGroupValue,v=m.templateCreateVisible,b=m.moveToOtherGroupVisible,y=m.beforeMovingTemp,h=m.afterMovingGroup,g=m.renameGroupVisible,E=m.renameGroup,k="classic"===m.templateType?a:n&&n.enable;return C.createElement(O.a,{className:"review-templatemgt"},C.createElement(q,{className:M.a.header},C.createElement(s.a,{className:M.a.groupBtn,type:"primary",ghost:!0,icon:"folder-add",onClick:this.showAddGroupModal},"\u6dfb\u52a0\u5206\u7ec4"),C.createElement(w.a,{to:"/work/review/tempGroupSort"},C.createElement(s.a,{className:M.a.groupBtn,type:"primary",ghost:!0,icon:"sort-ascending"},"\u5206\u7ec4\u6392\u5e8f")),C.createElement(s.a,{type:"primary",className:M.a.create,icon:"plus",onClick:this.showTemplateCreateModal},"\u65b0\u5efa\u8bc4\u5ba1\u6a21\u677f")),C.createElement(H,{className:M.a.templateList},C.createElement(c.a,{spinning:p},n&&n.enable.map(function(t,a){var n=t.id,r=t.name,o=t.count,c=t.data;return C.createElement("div",{key:a},C.createElement(x.a,{type:r,count:o,onRenameGroup:function(){return e.handleRenameGroup(n,r)},onDeleteGroup:function(){return e.handleDeleteGroup(n)}}),c&&c.map(function(t,a){return C.createElement(T.a,{key:a,pathname:"/work/review/createTemplate",type:"template",params:t,changeEnableStateUrl:"/api/c_pst_switch_pst_template_show",onEnableStateChange:e.handleEnableStateChange,onGroupMove:function(){return e.handleGroupMove(n,t.id,t.name)}})}))}),C.createElement(l.a,{type:"flex",style:{marginTop:"10px",padding:"0 8px",lineHeight:"40px",background:"#f9f9f9"}},C.createElement(i.a,{span:4},C.createElement("span",{style:{color:"#222"}},"\u5df2\u7981\u7528"))),n&&n.disable.map(function(t,a){return C.createElement(T.a,{key:a,params:t,type:"template",changeEnableStateUrl:"/api/c_pst_switch_pst_template_show",deleteUrl:"/api/c_pst_delete_pst_template",onEnableStateChange:e.handleEnableStateChange,onDeleteChange:e.handleDeleteChange})}))),C.createElement(r.a,{visible:d,title:"\u6dfb\u52a0\u5206\u7ec4",onOk:this.addGroup,onCancel:this.cancelGroup},C.createElement(o.a,{onChange:this.addGroupChange,value:f,maxLength:12,placeholder:"\u6700\u591a10\u4e2a\u5b57"})),C.createElement(r.a,{visible:g,title:"\u91cd\u547d\u540d\u5206\u7ec4",onOk:this.renameGroup,onCancel:this.cancelRenameGroup,okButtonProps:{disabled:!E||E.disabled}},C.createElement(o.a,{onChange:this.renameGroupChange,value:E&&E.name,maxLength:12,placeholder:"\u6700\u591a10\u4e2a\u5b57"})),C.createElement(r.a,{closable:!1,visible:b,title:'\u79fb\u52a8 "'.concat(y&&y.name,'" \u81f3'),onOk:this.moveToOtherGroup,onCancel:this.cancelMove,bodyStyle:{padding:0,maxHeight:250,overflowY:"auto"}},u&&u.map(function(t){var a=t.id,n=t.name;return C.createElement("div",{key:a,className:N()("clearfix",M.a.tempGroupItem,h&&Object(S.a)(h.id)===Object(S.a)(a)?M.a.active:""),onClick:function(){return e.handleMoveGroupChecked(a)}},C.createElement(P.a,{text:n,colon:!1}),C.createElement("span",{className:"pull-right"},y&&Object(S.a)(y.groupId)===Object(S.a)(a)?"\u5f53\u524d\u6240\u5728\u7ec4":""))})),C.createElement(r.a,{visible:v,title:"\u521b\u5efa\u8bc4\u5ba1\u6a21\u677f",onCancel:this.cancelTemplateList,footer:null,width:800,getContainer:function(){return document.getElementsByClassName("work-review")[0]},wrapClassName:"modal-review"},C.createElement(l.a,{className:"modal-review-sectitle"},C.createElement(i.a,{span:18},C.createElement(R,{onChange:this.handleTempTypeChange,defaultValue:"classicTemplate",buttonStyle:"solid"},C.createElement(D,{value:"classicTemplate"},"\u4f7f\u7528\u63a8\u8350\u6a21\u677f"),C.createElement(D,{value:"existingTemplate"},"\u4f7f\u7528\u5df2\u6709\u6a21\u677f"))),C.createElement(i.a,{span:6,style:{textAlign:"right"}},C.createElement(w.a,{to:"/work/review/createTemplate"},C.createElement(s.a,{onClick:this.cancelTemplateList,type:"primary",ghost:!0,icon:"plus"},"\u81ea\u5b9a\u4e49\u6a21\u677f")))),C.createElement("div",{className:"templateWrapper"},C.createElement(_.a,{className:"modal-review-list",datasource:k,pathname:"/work/review/createTemplate",onItemClick:this.cancelTemplateList}))))}}]),t}(C.Component))||n}}]);
//# sourceMappingURL=35.205bcb4a.chunk.js.map