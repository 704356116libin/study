(window.webpackJsonp=window.webpackJsonp||[]).push([[43],{758:function(e,t,a){"use strict";var r=a(759),n=a.n(r);a(868),a(869);t.a=n.a},869:function(e,t,a){},955:function(e,t,a){},986:function(e,t,a){"use strict";a.r(t);a(237);var r=a(157),n=(a(234),a(156)),i=(a(461),a(462)),o=(a(459),a(460)),c=(a(120),a(27)),s=(a(233),a(13)),l=a(2),p=a.n(l),d=(a(58),a(12)),u=a(4),m=a(57),h=a(42),f=a(43),v=a(45),b=a(44),w=a(46),y=a(0),g=a(3),E=a(758),x=(a(955),function(e){function t(){var e,a;Object(h.a)(this,t);for(var r=arguments.length,n=new Array(r),i=0;i<r;i++)n[i]=arguments[i];return(a=Object(v.a)(this,(e=Object(b.a)(t)).call.apply(e,[this].concat(n)))).state={template:{},reportValue:void 0,visible:!1},a.handleExport=Object(m.a)(p.a.mark(function e(){var t,r,n;return p.a.wrap(function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(g.a)("/api/c_pst_exportSingleTemplatePackage",{getFile:!0,method:"POST",body:Object(u.a)({},a.state.template,{text:a.state.reportValue.toHTML()})});case 2:t=e.sent,r=window.URL.createObjectURL(t.blob),(n=document.createElement("a")).download=decodeURI(t.headers.get("filename")),n.href=r,n.click(),window.URL.revokeObjectURL(r),d.a.info("\u5bfc\u51fa\u6210\u529f");case 10:case"end":return e.stop()}},e)})),a.handlePrint=function(){var e=document.getElementById("print"),t="\n    <body>".concat(a.state.reportValue.toHTML(),"</body>\n    ");e.contentDocument.write(t),e.contentDocument.close(),e.contentWindow.print()},a.preview=function(){a.setState({visible:!0})},a}return Object(w.a)(t,e),Object(f.a)(t,[{key:"componentDidMount",value:function(){var e=this;if(this.props.location.state){var t=this.props.location.state,a=t.pst_id,r=t.report_id;Object(m.a)(p.a.mark(function t(){var n;return p.a.wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,Object(g.b)("/api/c_pst_getReplacedVarTemplate",{params:{pst_id:a,temId:r}});case 2:"success"===(n=t.sent).status&&e.setState({template:n.data,reportValue:E.a.createEditorState(n.data.text)});case 4:case"end":return t.stop()}},t)}))()}}},{key:"render",value:function(){var e=this,t=this.state,a=t.reportValue,l=t.visible;return y.createElement(r.a,{className:"review-export-preview"},y.createElement("div",{style:{marginBottom:24,padding:"0 20px",height:"56px",lineHeight:"56px",border:"1px solid #eee"}},y.createElement("span",{className:"goback",onClick:function(){return e.props.history.goBack()}},y.createElement(s.a,{type:"arrow-left"}),"\u8fd4\u56de")),y.createElement(i.a,null,y.createElement(o.a,{span:4,className:"text-right",style:{paddingRight:36}},y.createElement("div",{style:{marginBottom:12}},y.createElement(c.a,{type:"primary",style:{width:160},onClick:this.handleExport},"\u4e0b\u8f7d")),y.createElement("div",{style:{marginBottom:12}},y.createElement(c.a,{type:"primary",style:{width:160},onClick:this.preview},"\u9884\u89c8")),y.createElement("div",{style:{marginBottom:12}},y.createElement(c.a,{type:"primary",style:{width:160},onClick:this.handlePrint},"\u6253\u5370"))),y.createElement(o.a,{span:20},y.createElement(E.a,{className:"create-report",value:a,controls:["undo","redo","remove-styles","separator","headings","font-size","font-family","separator","bold","italic","underline","text-color","separator","list-ul","list-ol","text-indent","text-align","separator","superscript","subscript","media","fullscreen"],onChange:function(t){return e.setState({reportValue:t})}}))),y.createElement(n.a,{visible:l,title:"\u9884\u89c8",width:790,footer:null,onCancel:function(){return e.setState({visible:!1})}},y.createElement("div",{dangerouslySetInnerHTML:{__html:this.state.reportValue?this.state.reportValue.toHTML():""}})),y.createElement("iframe",{id:"print",src:"",width:"0",height:"0",frameBorder:"0"}))}}]),t}(y.Component));t.default=x}}]);
//# sourceMappingURL=43.2267a34d.chunk.js.map