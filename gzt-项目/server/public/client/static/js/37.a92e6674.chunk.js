(window.webpackJsonp=window.webpackJsonp||[]).push([[37],{1001:function(e,t,a){"use strict";a.r(t),a.d(t,"default",function(){return O});a(155);var n,r=a(94),o=(a(456),a(458)),c=(a(233),a(13)),l=(a(58),a(12)),i=a(42),s=a(43),p=a(45),u=a(44),f=a(46),d=a(4),m=(a(237),a(157)),y=(a(525),a(520)),v=a(0),b=a(51),h=a(924),g=(a(873),y.a.Option),E=m.a.Header,O=Object(b.c)(function(e){return Object(d.a)({},e.Approval,{listLoading:e.loading.effects["".concat("Approval","/queryApplyTemplateList")]})},function(e){return{showApplyTemplate:function(t){e({type:"".concat("Approval","/queryApplyTemplateList"),payload:t})},showApprovalTypeList:function(){e({type:"".concat("Approval","/queryTemplateSelectList"),payload:{}})},showSearchTypeInfo:function(t){e({type:"".concat("Approval","/queryTypeTemplateInfo"),payload:t})}}})(n=function(e){function t(){var e,a;Object(i.a)(this,t);for(var n=arguments.length,r=new Array(n),o=0;o<n;o++)r[o]=arguments[o];return(a=Object(p.a)(this,(e=Object(u.a)(t)).call.apply(e,[this].concat(r)))).state={focus:!1,searchValue:"",typeId:-1},a.emitEmpty=function(){a.setState({focus:!1,searchValue:""})},a.onFocus=function(){a.setState({focus:!0})},a.handlePressEnter=function(e){var t=e.target.value;""===t?l.a.info("\u8bf7\u8f93\u5165\u7c7b\u578b\u547d\u540d\u79f0\u8fdb\u884c\u641c\u7d22~"):a.props.showSearchTypeInfo({keyWords:t})},a.handleChange=function(e){-1===e&&(e="all"),a.props.showApplyTemplate({type_id:e})},a}return Object(f.a)(t,e),Object(s.a)(t,[{key:"componentDidMount",value:function(){this.props.showApplyTemplate({type_id:-1===this.state.typeId?"all":""})}},{key:"render",value:function(){var e=this,t=this.props,a=t.applyTemplateList,n=t.templateSelectInfo,l=t.listLoading;console.log(n,"templateSelectInfo");var i=this.state.typeId;return v.createElement(m.a,null,v.createElement(E,{className:"white",style:{lineHeight:"73px",height:"73px"}},v.createElement(o.a,{placeholder:"\u8bf7\u8f93\u5165\u9879\u76ee\u8fdb\u884c\u67e5\u8be2",onPressEnter:function(t){return e.handlePressEnter(t)},prefix:v.createElement(c.a,{type:"search",style:{color:"rgba(0,0,0,.25)"}}),style:{width:"240px",marginLeft:"30px"},allowClear:!0}),v.createElement(y.a,{style:{marginLeft:"30px",width:150},defaultValue:i,onChange:this.handleChange},v.createElement(g,{value:i},"\u5168\u90e8"),n&&n.map(function(e,t){var a=e.type_id,n=e.name;return v.createElement(g,{value:a,key:t},n)}))),v.createElement(r.a,{spinning:l,delay:300},v.createElement("div",{style:{padding:"10px 10px 20px 30px"}},v.createElement(h.a,{datasource:a,link:"/work/approval/template"}))))}}]),t}(v.Component))||n},459:function(e,t,a){"use strict";a(24),a(463)},460:function(e,t,a){"use strict";var n=a(643);t.a=n.a},461:function(e,t,a){"use strict";a(24),a(463)},462:function(e,t,a){"use strict";var n=a(642);t.a=n.a},603:function(e,t,a){"use strict";a(24),a(628),a(550),a(461),a(459)},604:function(e,t,a){"use strict";var n=a(0),r=a(6),o=a.n(r),c=a(32),l=a(11);function i(){return(i=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var s=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},p=function(e){return n.createElement(l.a,null,function(t){var a=t.getPrefixCls,r=e.prefixCls,c=e.className,l=s(e,["prefixCls","className"]),p=a("card",r),u=o()("".concat(p,"-grid"),c);return n.createElement("div",i({},l,{className:u}))})};function u(){return(u=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}var f=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},d=function(e){return n.createElement(l.a,null,function(t){var a=t.getPrefixCls,r=e.prefixCls,c=e.className,l=e.avatar,i=e.title,s=e.description,p=f(e,["prefixCls","className","avatar","title","description"]),d=a("card",r),m=o()("".concat(d,"-meta"),c),y=l?n.createElement("div",{className:"".concat(d,"-meta-avatar")},l):null,v=i?n.createElement("div",{className:"".concat(d,"-meta-title")},i):null,b=s?n.createElement("div",{className:"".concat(d,"-meta-description")},s):null,h=v||b?n.createElement("div",{className:"".concat(d,"-meta-detail")},v,b):null;return n.createElement("div",u({},p,{className:m}),y,h)})},m=a(552),y=a(462),v=a(460),b=a(21);function h(e){return(h="function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"===typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function g(){return(g=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n])}return e}).apply(this,arguments)}function E(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function O(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function w(e,t){return!t||"object"!==h(t)&&"function"!==typeof t?function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e):t}function x(e){return(x=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}function N(e,t){return(N=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e})(e,t)}a.d(t,"a",function(){return C});var j=function(e,t){var a={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(a[n]=e[n]);if(null!=e&&"function"===typeof Object.getOwnPropertySymbols){var r=0;for(n=Object.getOwnPropertySymbols(e);r<n.length;r++)t.indexOf(n[r])<0&&(a[n[r]]=e[n[r]])}return a},C=function(e){function t(){var e;return function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,t),(e=w(this,x(t).apply(this,arguments))).onTabChange=function(t){e.props.onTabChange&&e.props.onTabChange(t)},e.renderCard=function(t){var a,r,l=t.getPrefixCls,i=e.props,s=i.prefixCls,p=i.className,u=i.extra,f=i.headStyle,d=void 0===f?{}:f,b=i.bodyStyle,h=void 0===b?{}:b,O=(i.noHovering,i.hoverable,i.title),w=i.loading,x=i.bordered,N=void 0===x||x,C=i.size,k=void 0===C?"default":C,P=i.type,S=i.cover,T=i.actions,A=i.tabList,H=i.children,L=i.activeTabKey,_=i.defaultActiveTabKey,I=j(i,["prefixCls","className","extra","headStyle","bodyStyle","noHovering","hoverable","title","loading","bordered","size","type","cover","actions","tabList","children","activeTabKey","defaultActiveTabKey"]),z=l("card",s),K=o()(z,p,(E(a={},"".concat(z,"-loading"),w),E(a,"".concat(z,"-bordered"),N),E(a,"".concat(z,"-hoverable"),e.getCompatibleHoverable()),E(a,"".concat(z,"-contain-grid"),e.isContainGrid()),E(a,"".concat(z,"-contain-tabs"),A&&A.length),E(a,"".concat(z,"-").concat(k),"default"!==k),E(a,"".concat(z,"-type-").concat(P),!!P),a)),q=0===h.padding||"0px"===h.padding?{padding:24}:void 0,M=n.createElement("div",{className:"".concat(z,"-loading-content"),style:q},n.createElement(y.a,{gutter:8},n.createElement(v.a,{span:22},n.createElement("div",{className:"".concat(z,"-loading-block")}))),n.createElement(y.a,{gutter:8},n.createElement(v.a,{span:8},n.createElement("div",{className:"".concat(z,"-loading-block")})),n.createElement(v.a,{span:15},n.createElement("div",{className:"".concat(z,"-loading-block")}))),n.createElement(y.a,{gutter:8},n.createElement(v.a,{span:6},n.createElement("div",{className:"".concat(z,"-loading-block")})),n.createElement(v.a,{span:18},n.createElement("div",{className:"".concat(z,"-loading-block")}))),n.createElement(y.a,{gutter:8},n.createElement(v.a,{span:13},n.createElement("div",{className:"".concat(z,"-loading-block")})),n.createElement(v.a,{span:9},n.createElement("div",{className:"".concat(z,"-loading-block")}))),n.createElement(y.a,{gutter:8},n.createElement(v.a,{span:4},n.createElement("div",{className:"".concat(z,"-loading-block")})),n.createElement(v.a,{span:3},n.createElement("div",{className:"".concat(z,"-loading-block")})),n.createElement(v.a,{span:16},n.createElement("div",{className:"".concat(z,"-loading-block")})))),G=void 0!==L,V=E({},G?"activeKey":"defaultActiveKey",G?L:_),D=A&&A.length?n.createElement(m.a,g({},V,{className:"".concat(z,"-head-tabs"),size:"large",onChange:e.onTabChange}),A.map(function(e){return n.createElement(m.a.TabPane,{tab:e.tab,disabled:e.disabled,key:e.key})})):null;(O||u||D)&&(r=n.createElement("div",{className:"".concat(z,"-head"),style:d},n.createElement("div",{className:"".concat(z,"-head-wrapper")},O&&n.createElement("div",{className:"".concat(z,"-head-title")},O),u&&n.createElement("div",{className:"".concat(z,"-extra")},u)),D));var F=S?n.createElement("div",{className:"".concat(z,"-cover")},S):null,J=n.createElement("div",{className:"".concat(z,"-body"),style:h},w?M:H),B=T&&T.length?n.createElement("ul",{className:"".concat(z,"-actions")},e.getAction(T)):null,R=Object(c.default)(I,["onTabChange"]);return n.createElement("div",g({},R,{className:K}),r,F,J,B)},e}var a,r,i;return function(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),t&&N(e,t)}(t,n["Component"]),a=t,(r=[{key:"componentDidMount",value:function(){"noHovering"in this.props&&(Object(b.a)(!this.props.noHovering,"Card","`noHovering` is deprecated, you can remove it safely or use `hoverable` instead."),Object(b.a)(!!this.props.noHovering,"Card","`noHovering={false}` is deprecated, use `hoverable` instead."))}},{key:"isContainGrid",value:function(){var e;return n.Children.forEach(this.props.children,function(t){t&&t.type&&t.type===p&&(e=!0)}),e}},{key:"getAction",value:function(e){return e.map(function(t,a){return n.createElement("li",{style:{width:"".concat(100/e.length,"%")},key:"action-".concat(a)},n.createElement("span",null,t))})}},{key:"getCompatibleHoverable",value:function(){var e=this.props,t=e.noHovering,a=e.hoverable;return"noHovering"in this.props?!t||a:!!a}},{key:"render",value:function(){return n.createElement(l.a,null,this.renderCard)}}])&&O(a.prototype,r),i&&O(a,i),t}();C.Grid=p,C.Meta=d},628:function(e,t,a){},873:function(e,t,a){},924:function(e,t,a){"use strict";a.d(t,"a",function(){return d});a(154);var n=a(93),r=(a(461),a(462)),o=(a(459),a(460)),c=(a(237),a(157)),l=(a(603),a(604)),i=a(0),s=a.n(i),p=a(89),u=l.a.Meta,f=c.a.Content;function d(e){var t=e.datasource,a=e.link,c=e.onCloseModal;return Object(i.useEffect)(function(){}),s.a.createElement(f,null,t&&t.map(function(e,t){var i=e.type_name,f=e.data;return 0===f.length?null:s.a.createElement("div",{key:t},s.a.createElement(r.a,{className:"typeTitle",style:{margin:"5px 0",background:"#f8f8f8",borderLeft:"3px solid #1890ff"}},s.a.createElement(o.a,{style:{padding:"10px 5px",color:"#000",fontSize:"16px"},className:"overflow-ellipsis"},i)),s.a.createElement("div",{className:"clearfix",onClick:function(){return c&&c()}},f&&f.map(function(e,t){var r=e.name,o=e.id,c=e.desc;return s.a.createElement(p.a,{key:t,to:{pathname:a,state:{type:"insert",id:o,name:r,desc:c}},className:"templateBox"},s.a.createElement(l.a,{size:"small",hoverable:!0,bordered:!0,style:{float:"left",margin:"0 16px 16px 0",width:220}},s.a.createElement(u,{avatar:s.a.createElement(n.a,{shape:"square",icon:"user",style:{background:"#3497FA"},size:50}),title:s.a.createElement("span",{style:{fontSize:14}},r),description:s.a.createElement("div",{className:"overflow-ellipsis",title:c},c)})))})))}))}}}]);
//# sourceMappingURL=37.a92e6674.chunk.js.map