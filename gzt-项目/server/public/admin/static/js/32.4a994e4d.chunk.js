(window.webpackJsonp=window.webpackJsonp||[]).push([[32],{494:function(e,t,a){},584:function(e,t,a){"use strict";a.r(t);a(394);var n=a(415),r=(a(274),a(276)),l=(a(255),a(254)),i=(a(153),a(113)),c=(a(275),a(277)),o=(a(416),a(417)),m=(a(106),a(14)),s=(a(80),a(36)),u=a(1),p=a.n(u),d=a(61),f=a(32),E=a(422),y=a(488),b=a.n(y),g=b.a.DataView,h=E.Guide.Html,_=new g,v={percent:{formatter:function(e){return e=100*e+"%"}}};function x(e){var t=e.dataSource;if(!t)return null;var a=t.use_number||0,n=[{item:"\u5df2\u7528\u6761\u6570",count:a},{item:"\u5269\u4f59\u6761\u6570",count:t.type_number-a}];return _.source(n).transform({type:"percent",field:"count",dimension:"item",as:"percent"}),p.a.createElement(E.Chart,{width:400,height:300,data:_,padding:40,scale:v,forceFit:!0},p.a.createElement(E.Coord,{type:"theta",radius:1,innerRadius:.6}),p.a.createElement(E.Axis,{name:"percent"}),p.a.createElement(E.Tooltip,{showTitle:!1,itemTpl:'<li><span style="background-color:{color};" class="g2-tooltip-marker"></span>{name}: {value}</li>'}),p.a.createElement(E.Guide,null,p.a.createElement(h,{position:["50%","50%"],html:"<div style=&quot;color:#8c8c8c;font-size:1.16em;text-align: center;width: 120px;overflow:hidden&quot;>\u77ed\u4fe1\u5269\u4f59<br><span style=&quot;color:#262626;font-size:2em&quot;>".concat(n.filter(function(e){return"\u5269\u4f59\u6761\u6570"===e.item})[0].count,"</span>\u6761</div>"),alignX:"middle",alignY:"middle"})),p.a.createElement(E.Geom,{type:"intervalStack",position:"percent",color:["item",["rgb(248, 189, 87)","rgb(255, 155, 212)"]],tooltip:["item*percent",function(e,t){return{name:e,value:"".concat(n.filter(function(t){return t.item===e})[0].count,"\u6761")}}],style:{lineWidth:1,stroke:"#fff"}}))}var w=new(0,b.a.DataView),z={percent:{formatter:function(e){return e=(100*e).toFixed(2)+"%"}}};function k(e){var t=e.dataSource;if(!t)return null;var a=[{item:"\u5458\u5de5\u4eba\u6570",count:t.staff_number.use_number},{item:"\u5408\u4f5c\u4f19\u4f34\u6570\u91cf",count:t.partner.use_number},{item:"\u5916\u90e8\u8054\u7cfb\u4eba\u6570",count:t.external_contact.use_number}];w.source(a).transform({type:"percent",field:"count",dimension:"item",as:"percent"});return p.a.createElement(E.Chart,{width:400,height:300,data:w,padding:[40,90,40,0],scale:z,forceFit:!0},p.a.createElement(E.Coord,{type:"theta",radius:1,innerRadius:.6}),p.a.createElement(E.Axis,{name:"percent"}),p.a.createElement(E.Legend,{position:"right",offsetY:-70,offsetX:-40,itemFormatter:function(e){return"".concat(e,": ").concat(a.filter(function(t){var a=t.item;t.count;return a===e})[0].count,"\u4eba")}}),p.a.createElement(E.Tooltip,{showTitle:!1,itemTpl:'<li><span style="background-color:{color};" class="g2-tooltip-marker"></span>{name}: {value}</li>'}),p.a.createElement(E.Geom,{type:"intervalStack",position:"percent",color:"item",tooltip:["item*percent",function(e,t){return{name:e,value:"".concat(a.filter(function(e){var t=e.item;return t===t})[0].count,"\u4eba")}}],style:{lineWidth:1,stroke:"#fff"}}))}var S=["B","KB","MB","GB","TB"];function N(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:1;return e/Math.pow(1024,t)>1?N(e,t+1):[Math.round(e/Math.pow(1024,t-1)*100)/100,S[t-1]]}var B=E.Guide.Text;function j(e){var t=e.dataSource;if(!t)return null;var a=[{gender:"\u4e91\u76d8\u5bb9\u91cf",path:"M381.759 0h292l-.64 295.328-100.127-100.096-94.368 94.368C499.808 326.848 512 369.824 512 415.712c0 141.376-114.56 256-256 256-141.376 0-256-114.624-256-256s114.624-256 256-256c48.8 0 94.272 13.92 133.12 37.632l93.376-94.592L381.76 0zM128.032 415.744c0 70.688 57.312 128 128 128s128-57.312 128-128-57.312-128-128-128-128 57.312-128 128z",value:t.type_number-t.use_number}],n={value:{min:0,max:t.type_number}};return p.a.createElement(E.Chart,{height:300,data:a,scale:n,padding:[0,40,0,40],forceFit:!0},p.a.createElement(E.Geom,{type:"interval",position:"gender*value",color:["gender","rgb(163, 221, 248)"],shape:"liquid-fill-gauge",style:{lineWidth:5,opacity:.75}}),p.a.createElement(E.Guide,null,a.map(function(e){return p.a.createElement(p.a.Fragment,null,p.a.createElement(B,{content:"\u5269\u4f59\u5bb9\u91cf".concat(N(e.value).join("")),top:!0,position:{gender:e.gender,value:e.value/2},style:{opacity:.75,fontSize:20,textAlign:"center"}}),p.a.createElement(B,{content:"\u7f51\u76d8\u603b\u91cf".concat(N(t.type_number).join("")),top:!0,position:{gender:e.gender,value:e.value/2},offsetY:-50,style:{opacity:.75,fontSize:20,textAlign:"center",fill:"#000"}}))})))}a(494);var C=s.a.Header,G=s.a.Content,F=m.a.createFromIconfontCN({scriptUrl:"//at.alicdn.com/t/font_1176129_kmrrudd78js.js",extraCommonProps:{color:"in"}});t.default=Object(f.c)(function(e){return{basisInfo:e.Basis.basisInfo}},function(e){return{queryBasisInfo:function(){e({type:"".concat("Basis","/queryBasisInfo")})}}})(function(e){var t=e.basisInfo,a=e.queryBasisInfo;return Object(u.useEffect)(function(){a()},[]),p.a.createElement(s.a,{style:{marginTop:"64px",overflow:"hidden"}},p.a.createElement(C,{className:"index-header"},p.a.createElement(r.a,{type:"flex",align:"middle"},p.a.createElement(c.a,{span:6,className:"index-name"},p.a.createElement(o.a,{size:64,src:t&&t.base_info.logo&&t.base_info.logo.url},t&&t.base_info.abbreviation),p.a.createElement("div",null,t&&t.base_info.name,p.a.createElement(d.a,{to:"/information",style:{marginLeft:12}},"\u7f16\u8f91"))),p.a.createElement(c.a,{span:18,className:"index-cert"},t?1===t.base_info.verified?p.a.createElement(p.a.Fragment,null,p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u53ef\u4ee5\u81ea\u5b9a\u4e49\u4f01\u4e1alogo"},p.a.createElement("span",{className:"index-cert-has"},p.a.createElement(F,{style:{fontSize:24},type:"icon-dingzhijiaju"}))),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u589e\u52a020\u4eba"},p.a.createElement(o.a,{size:40,style:{background:"#1890ff"},icon:"team"})),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u589e\u52a030\u6761"},p.a.createElement("span",{className:"index-cert-has"},p.a.createElement(F,{style:{fontSize:24},type:"icon-message"}))),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u6269\u5bb9 3G"},p.a.createElement("span",{className:"index-cert-has"},p.a.createElement(F,{style:{fontSize:24},type:"icon-wangpan"})))):p.a.createElement(p.a.Fragment,null,p.a.createElement(i.a,{title:"\u53ef\u4ee5\u81ea\u5b9a\u4e49\u4f01\u4e1alogo"},p.a.createElement("span",{className:"index-cert-no"},p.a.createElement(F,{style:{fontSize:24},type:"icon-dingzhijiaju"}))),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u589e\u52a020\u4eba"},p.a.createElement(o.a,{size:40,icon:"team"})),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u589e\u52a030\u6761"},p.a.createElement("span",{className:"index-cert-no"},p.a.createElement(F,{style:{fontSize:24},type:"icon-message"}))),p.a.createElement(i.a,{title:"\u8ba4\u8bc1\u540e\u6700\u9ad8\u6269\u5bb9 3G"},p.a.createElement("span",{className:"index-cert-no"},p.a.createElement(F,{style:{fontSize:24},type:"icon-wangpan"}))),p.a.createElement(d.a,{to:"/license"},p.a.createElement(l.a,{type:"primary",style:{background:"#00AEB7",borderColor:"#00AEB7"}},"\u7533\u8bf7\u4f01\u4e1a\u8ba4\u8bc1"))):null))),p.a.createElement(G,{className:"index-con"},p.a.createElement(r.a,{className:"index-sec",type:"flex",align:"middle",style:{padding:"24px 0"}},p.a.createElement(c.a,{span:6,style:{textAlign:"center",borderRight:"1px solid #ddd"}},p.a.createElement(n.a,{type:"circle",percent:t?Math.round(t.base_limit.staff_number.use_number/t.base_limit.staff_number.type_number*100):0})),p.a.createElement(c.a,{span:12,style:{padding:"0 24px"}},p.a.createElement("div",{style:{display:"inline-block",marginRight:"24px"}},p.a.createElement("p",null,"\u5f53\u524d\u53ef\u7528\u4eba\u6570"),p.a.createElement("span",{className:"index-per"},t&&t.base_limit.staff_number.type_number,"\u4eba")),p.a.createElement("div",{style:{display:"inline-block"}},p.a.createElement("p",null,"\u5269\u4f59\u53ef\u7528\u4eba\u6570"),p.a.createElement("span",{className:"index-per"},t&&t.base_limit.staff_number.type_number-t.base_limit.staff_number.use_number,"\u4eba"))),p.a.createElement(c.a,{span:6,style:{textAlign:"center"}},p.a.createElement(d.a,{to:"/buy/people"},p.a.createElement(l.a,{type:"primary",ghost:!0},"\u8d2d\u4e70\u540d\u989d")))),p.a.createElement(r.a,{className:"index-sec text-center",style:{paddingBottom:"12px"}},p.a.createElement(c.a,{span:8},p.a.createElement(k,{dataSource:t&&{staff_number:t.base_limit.staff_number,partner:t.base_limit.partner,external_contact:t.base_limit.external_contact}}),p.a.createElement(d.a,{to:"/buy/people"},p.a.createElement(l.a,{type:"primary",ghost:!0},"\u6269\u5bb9"))),p.a.createElement(c.a,{span:8},p.a.createElement(j,{dataSource:t&&t.base_limit.disk}),p.a.createElement(d.a,{to:"/buy/netdisc"},p.a.createElement(l.a,{type:"primary",ghost:!0},"\u6269\u5bb9"))),p.a.createElement(c.a,{span:8},p.a.createElement(x,{dataSource:t&&t.base_limit.sms}),p.a.createElement(d.a,{to:"/buy/sms"},p.a.createElement(l.a,{type:"primary",ghost:!0},"\u5145\u503c"))))))})}}]);
//# sourceMappingURL=32.4a994e4d.chunk.js.map