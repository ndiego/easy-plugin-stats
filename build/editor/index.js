(()=>{"use strict";const e=window.React,t=window.wp.hooks,l=window.wp.i18n,n=window.wp.blocks,a=window.wp.primitives,s=(0,e.createElement)(a.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,e.createElement)(a.Path,{d:"M10.5 4v4h3V4H15v4h1.5a1 1 0 011 1v4l-3 4v2a1 1 0 01-1 1h-3a1 1 0 01-1-1v-2l-3-4V9a1 1 0 011-1H9V4h1.5zm.5 12.5v2h2v-2l3-4v-3H8v3l3 4z"}));(0,n.registerBlockVariation)("core/button",{name:"easy-plugin-stats/button",icon:s,title:(0,l.__)("Plugin Link","easy-plugin-stats"),description:(0,l.__)("Display a button that links to resources associated with plugins hosted on WordPress.org.","easy-plugin-stats"),scope:["inserter","transform"],attributes:{metadata:{bindings:{url:{source:"easy-plugin-stats/button",args:{field:"download",slug:""}}}}},isActive:e=>"easy-plugin-stats/button"===e?.metadata?.bindings?.url?.source});const i=window.wp.blockEditor,o=window.wp.components;function r(t){var n,a,s;const{attributes:r,setAttributes:u}=t,{metadata:g}=r;if("easy-plugin-stats/button"!==g?.bindings?.url?.source)return null;const p=null!==(n=g?.bindings?.url?.args?.slug)&&void 0!==n?n:"",d=null!==(a=g?.bindings?.url?.args?.field)&&void 0!==a?a:"download",c=null!==(s=g?.bindings?.url?.args?.cache)&&void 0!==s?s:"",_=[{label:(0,l.__)("Plugin Homepage","easy-plugin-stats"),value:"homepage_link"},{label:(0,l.__)("Download Link","easy-plugin-stats"),value:"download_link"},{label:(0,l.__)("Live Preview","easy-plugin-stats"),value:"live_preview_link"},{label:(0,l.__)("Support Forum","easy-plugin-stats"),value:"support_link"},{label:(0,l.__)("Reviews","easy-plugin-stats"),value:"reviews_link"},{label:(0,l.__)("Author Profile","easy-plugin-stats"),value:"author_profile"},{label:(0,l.__)("Donate Link","easy-plugin-stats"),value:"donate_link"}],m=(e,t)=>{u({metadata:{...g,bindings:{...g.bindings,url:{...g.bindings.url,args:{...g.bindings.url.args,[e]:t}}}}})};return(0,e.createElement)(i.InspectorControls,{group:"settings"},(0,e.createElement)(o.__experimentalToolsPanel,{label:(0,l.__)("Link settings","easy-plugin-stats"),resetAll:()=>{u({metadata:{...g,bindings:{...g.bindings,url:{...g.bindings.url,args:{field:"homepage_link",slug:"",cache:""}}}}})},dropdownMenuProps:{popoverProps:{placement:"left-start",offset:259}}},(0,e.createElement)(o.__experimentalToolsPanelItem,{label:(0,l.__)("Plugin slug","easy-plugin-stats"),hasValue:()=>p,onDeselect:()=>m("slug",""),isShownByDefault:!0},(0,e.createElement)(o.__experimentalInputControl,{label:(0,l.__)("Plugin Slug","easy-plugin-stats"),value:p,onChange:e=>m("slug",e),type:"text",help:(0,l.__)("The plugin slug on WordPress.org.","easy-plugin-stats")})),(0,e.createElement)(o.__experimentalToolsPanelItem,{label:(0,l.__)("Link to","easy-plugin-stats"),hasValue:()=>d&&"homepage_link"!==d,onDeselect:()=>m("field","homepage_link"),isShownByDefault:!0},(0,e.createElement)(o.SelectControl,{label:(0,l.__)("Link to","easy-plugin-stats"),value:d,options:_,onChange:e=>m("field",e)})),(0,e.createElement)(o.__experimentalToolsPanelItem,{label:(0,l.__)("Cache"),hasValue:()=>c,onDeselect:()=>m("cache","")},(0,e.createElement)(o.__experimentalInputControl,{label:(0,l.__)("Cache (seconds)","easy-plugin-stats"),value:c,onChange:e=>m("cache",e),type:"number",help:(0,l.__)("WordPress.org plugin data is cached for 43200 seconds (12 hours) by default.","easy-plugin-stats"),placeholder:"43200",min:"3600"}))))}(0,t.addFilter)("editor.BlockEdit","easy-plugin-stats/add-inspector-controls",(function(t){return l=>"core/button"===l?.name?(0,e.createElement)(e.Fragment,null,(0,e.createElement)(t,{...l}),(0,e.createElement)(r,{...l})):(0,e.createElement)(t,{...l})}))})();