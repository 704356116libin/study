(window.webpackJsonp=window.webpackJsonp||[]).push([[50],{946:function(e,t,n){},982:function(e,t,n){"use strict";n.r(t),n.d(t,"default",function(){return C});n(237);var a,r=n(157),o=(n(261),n(169)),i=(n(233),n(13)),c=(n(254),n(20)),s=n(42),p=n(43),l=n(45),u=n(44),h=n(177),y=n(46),m=n(4),v=n(0),b=n(51),d=n(89),f=n(6),k=n.n(f),w=n(134),g=(n(946),{notice:["/work/notice","\u516c\u544a"],review:["/work/review","\u8bc4\u5ba1\u901a"],approval:["/work/approval","\u5ba1\u6279"],assist:["/work/assist","\u534f\u52a9"],communication:["/work/communication","\u6c9f\u901a"]}),C=Object(b.c)(function(e){return Object(m.a)({},e.Workbench,{permission:e.UserInfo.permission})},function(e){return{showNavigation:function(t,n){e({type:"".concat("Workbench","/setNavigation"),payload:{nav:t,k:n}})},showTabActive:function(t){e({type:"".concat("Workbench","/setTabActive"),payload:t})},queryCompanys:function(){e({type:"".concat("Workbench","/queryCompanys")})},changeCompany:function(t,n){e({type:"".concat("Workbench","/changeCompany"),payload:{id:t,cb:n}})},queryUserPermission:function(){e({type:"".concat("UserInfo","/queryUserPermission")})}}})(a=function(e){function t(e){var n;return Object(s.a)(this,t),(n=Object(l.a)(this,Object(u.a)(t).call(this,e))).state={canJump:!1,prevTab:"",needClear:!1,icon:"caret-down"},n.companyChange=function(e){var t=e.key;n.props.changeCompany(t,function(){var e=!0,t=!1,a=void 0;try{for(var r,o=Object(w.getCachingKeys)()[Symbol.iterator]();!(e=(r=o.next()).done);e=!0){var i=r.value;Object(w.dropByCacheKey)(i)}}catch(c){t=!0,a=c}finally{try{e||null==o.return||o.return()}finally{if(t)throw a}}n.props.queryUserPermission()})},e.cacheLifecycles.didRecover(n.componentDidRecover.bind(Object(h.a)(n))),n}return Object(y.a)(t,e),Object(p.a)(t,[{key:"componentDidMount",value:function(){var e=this.props.location.pathname;if(this.props.queryCompanys(),this.props.location.state){var t=g[e.split("/")[2].split("-")[0]],n=g[e.split("/")[2].split("-")[0]][0];this.props.showTabActive(n),this.props.showNavigation(t)}else"/work"===e&&"/work"!==this.props.currentTab&&this.props.history.replace(this.props.currentTab)}},{key:"componentDidUpdate",value:function(e){if(this.props.needJump&&(e.currentTab!==this.props.currentTab?this.props.history.replace(this.props.currentTab):"/work"!==e.location.pathname&&"/work"===this.props.location.pathname&&e.currentTab!==this.props.currentTab&&this.props.history.replace(this.props.currentTab)),this.state.needClear){var t=!0,n=!1,a=void 0;try{for(var r,o=Object(w.getCachingKeys)()[Symbol.iterator]();!(t=(r=o.next()).done);t=!0){var i=r.value;i.includes(this.state.prevTab)&&Object(w.dropByCacheKey)(i)}}catch(c){n=!0,a=c}finally{try{t||null==o.return||o.return()}finally{if(n)throw a}}this.setState({needClear:!1})}}},{key:"componentDidRecover",value:function(){this.props.history.replace(this.props.currentTab)}},{key:"showTabActive",value:function(e){this.props.showTabActive(e)}},{key:"showNavigation",value:function(e,t){this.props.showNavigation(e,t),this.setState({prevTab:e[0],needClear:!0})}},{key:"changeIcon",value:function(e){var t=e?"caret-up":"caret-down";this.setState({icon:t})}},{key:"render",value:function(){var e=this,t=this.props,n=t.navigation,a=t.currentTab,s=t.companys,p=this.state.icon,l=v.createElement(c.b,{onClick:this.companyChange},s&&s.relate_companys.map(function(e){var t=e.id,n=e.name;return v.createElement(c.b.Item,{key:t},n)}),v.createElement(c.b.Divider,null),v.createElement(c.b.Item,{key:0},"\u79c1\u4eba\u5de5\u4f5c\u7a7a\u95f4"));return v.createElement(r.a,null,v.createElement("div",{className:"workbench-header clearfix"},v.createElement("div",{className:k()({active:"/work"===a})},v.createElement(d.a,{to:"/work",onClick:function(){return e.showTabActive("/work")},style:{display:"inline-block",padding:"0 20px"}},"\u5de5\u4f5c\u53f0")),n.map(function(t,n){return v.createElement("div",{key:t[0],className:k()({active:t[0]===a})},v.createElement(d.a,{onClick:function(){return e.showTabActive(t[0])},to:t[0],style:{display:"inline-block",padding:"0 30px"}},t[1]),v.createElement(i.a,{onClick:function(){return e.showNavigation(t,n)},type:"close",style:{padding:"0 6px",cursor:"pointer"}}))}),v.createElement(o.a,{overlay:l,trigger:["click"],className:"company",placement:"bottomRight",onVisibleChange:function(t){return e.changeIcon(t)}},v.createElement("span",{className:"ant-dropdown-link",style:{padding:"0 20px",cursor:"pointer"}},v.createElement("span",{style:{paddingRight:"8px"}},s&&s.current_company.name?s.current_company.name:"\u79c1\u4eba\u5de5\u4f5c\u7a7a\u95f4"),v.createElement(i.a,{type:p})))),this.props.children)}}]),t}(v.Component))||a}}]);
//# sourceMappingURL=50.33ee4606.chunk.js.map