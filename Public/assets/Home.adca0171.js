import{r as i,c as s,w as t,o as e,a,b as d,F as l,d as o,u as n,e as m,t as c,f as u,v as r,g as p,h,i as g,j as f,m as v,k,l as y}from"./index.d3e10b71.js";import{f as x}from"./teambition.261a6e78.js";import{c as b}from"./share.3f932f3a.js";import{_ as w,i as C,C as I,f as R,d as _}from"./clipboard.8dc48f35.js";const j={class:"mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3",style:{"border-radius":"8px"}},M={class:"mdui-list"},D=p('<li class="mdui-list-item mdui-ripple"><div class="mdui-row mdui-col-xs-12"><div class="mdui-col-xs-8 mdui-col-sm-6">文件</div><div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">修改时间</div><div class="mdui-col-sm-1 mdui-hidden-sm-down mdui-text-right">大小</div><div class="mdui-col-xs-4 mdui-col-sm-2  mdui-text-right">操作</div></div></li>',1),F=d("div",{class:"mdui-col-xs-12"},[d("a",{href:"javascript:void(0);"},[d("i",{class:"mdui-icon material-icons"},"arrow_back"),h(" 返回上级 ")])],-1),q={key:1,class:"mdui-list-item mdui-ripple"},E=d("div",{class:"mdui-col-xs-12"},[d("i",{class:"mdui-icon material-icons"},"info"),h(" 没有更多数据呦")],-1),L={class:"mdui-row mdui-col-xs-12"},$={class:"mdui-col-xs-8 mdui-col-sm-6 mdui-text-truncate"},S={key:0,"data-name":"{{ node.name }}",href:"javascript:void(0);","aria-label":"Folder"},H=d("i",{class:"mdui-icon material-icons"},"folder_open",-1),U={key:1,"data-name":"{{ node.name }}",href:"javascript:void(0);","aria-label":"File"},z=d("i",{class:"mdui-icon material-icons"}," insert_drive_file ",-1),P={class:"mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right"},V={class:"mdui-col-sm-1 mdui-hidden-sm-down mdui-text-right"},Y={key:0,class:"mdui-col-xs-4 mdui-col-sm-2 mdui-text-right"},A=d("i",{class:"mdui-icon material-icons"},"link",-1),B=d("i",{class:"mdui-icon material-icons"},"file_download",-1),G={key:1,class:"mdui-col-xs-4 mdui-col-sm-2 mdui-text-right"},J=d("div",{class:"mdui-col-xs-12 mdui-typo-body-1-opacity mdui-text-center"},[h(" 加载更多 "),d("i",{class:"mdui-icon material-icons"},"expand_more")],-1),K={class:"mdui-list-item mdui-ripple"},N={class:"mdui-col-xs-12 mdui-typo-body-1-opacity"},O={class:"mdui-dialog",id:"shareDialog"},Q=d("div",{class:"mdui-dialog-title"},"分享资源",-1),T={class:"mdui-dialog-content"},W={class:"mdui-textfield"},X=d("i",{class:"mdui-icon material-icons"},"link",-1),Z={class:"mdui-dialog-actions"},ii=d("button",{class:"mdui-btn mdui-ripple","mdui-dialog-close":"","mdui-dialog-cancel":""},"关闭",-1),si={expose:[],setup(p){Date.prototype.Format=function(i){let s={"M+":this.getMonth()+1,"d+":this.getDate(),"h+":this.getHours(),"m+":this.getMinutes(),"s+":this.getSeconds(),"q+":Math.floor((this.getMonth()+3)/3),S:this.getMilliseconds()};/(y+)/.test(i)&&(i=i.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length)));for(let t in s)new RegExp("("+t+")").test(i)&&(i=i.replace(RegExp.$1,1==RegExp.$1.length?s[t]:("00"+s[t]).substr((""+s[t]).length)));return i};const h=g(),si=f(),ti=i({parentId:"",list:[],item:{},limit:100,offset:0,totalCount:0,isRoot:1,loading:!1,shareLink:""}),ei=s((()=>_(si.query.nodeId,""))),ai=async()=>{ti.loading=!0,await x({nodeId:ei.value,offset:ti.offset,limit:ti.limit}).then((i=>{ti.loading=!1;const s=i.result;ti.limit=s.limit,ti.offset=s.offset,ti.totalCount=s.totalCount,ti.list=s.list,ti.item=s.item,ti.nodeId=s.item.nodeId,ti.parentId=s.item.parentId,ti.isRoot=s.isRoot}))},di=(i,s="folder")=>{if(ti.loading)return!1;"file"===s?h.push({name:"Preview",query:{nodeId:i}}):h.push({name:"Home",query:{nodeId:i}})};return t((()=>si.query.nodeId),(async i=>{await ai()})),e((()=>{ai()})),(i,s)=>(k(),a(l,null,[d("div",j,[d("ul",M,[D,ti.loading?(k(),a(w,{key:0,color:"mdui-color-blue-200"})):(k(),a(l,{key:1},[ti.isRoot?o("v-if",!0):(k(),a("li",{key:0,class:"mdui-list-item mdui-ripple",onClick:s[1]||(s[1]=i=>di(ti.parentId))},[F])),n(C)(ti.list)?(k(),a("li",q,[E])):(k(!0),a(l,{key:2},m(ti.list,(i=>(k(),a("li",{key:i.id,class:"mdui-list-item mdui-ripple",onClick:s=>di(i.nodeId,i.kind)},[d("div",L,[d("div",$,["folder"===i.kind?(k(),a("a",S,[H,d("span",null," "+c(i.name),1)])):(k(),a("a",U,[z,d("span",null," "+c(i.name),1)]))]),d("div",P,c(new Date(i.updated).Format("yyyy-MM-dd hh:mm:ss")),1),d("div",V,c("folder"===i.kind?"-":n(R)(i.size)),1),"file"===i.kind?(k(),a("div",Y,[d("a",{class:"mdui-btn mdui-ripple mdui-btn-icon share","aria-label":"Share","mdui-tooltip":"{content: '分享'}",onClick:y((s=>(async(i,s)=>{await b({nodeId:i,name:s}).then((i=>{const s=i.result;200===i.code&&null!==i.result?(ti.shareLink=window.location.origin+"/s/"+s,new v.Dialog("#shareDialog").open()):v.snackbar({message:":( "+i.msg,timeout:2e3,position:"right-top"})}))})(i.nodeId,i.name)),["stop"])},[A],8,["onClick"]),d("a",{class:"mdui-btn mdui-ripple mdui-btn-icon download","aria-label":"Download","mdui-tooltip":"{content: '下载'}",onClick:y((s=>{return t=i.downloadUrl,void window.open(t,"_blank");var t}),["stop"]),target:"_blank"},[B],8,["onClick"])])):(k(),a("div",G,"-"))])],8,["onClick"])))),128)),ti.totalCount>ti.list.length?(k(),a("li",{key:3,onClick:s[2]||(s[2]=i=>(async()=>{await x({nodeId:ei.value,offset:ti.offset,limit:ti.limit}).then((i=>{const s=i.result;ti.list=ti.list.concat(s.list)}))})()),class:"mdui-list-item mdui-ripple"},[J])):o("v-if",!0),d("li",K,[d("div",N,c(ti.totalCount)+" 个项目 ",1)])],64))])]),d("div",O,[Q,d("div",T,[d("div",W,[X,u(d("input",{class:"mdui-textfield-input",type:"text","onUpdate:modelValue":s[3]||(s[3]=i=>ti.shareLink=i),placeholder:"分享链接"},null,512),[[r,ti.shareLink]])])]),d("div",Z,[ii,d("button",{class:"mdui-btn mdui-ripple clipboard","data-clipboard-text":ti.shareLink,onClick:s[4]||(s[4]=i=>(()=>{const i=new I(".clipboard");i.on("success",(s=>{v.snackbar({message:":) 复制成功",timeout:2e3,position:"right-top"}),i.destroy()})),i.on("error",(s=>{console.error("该浏览器不支持自动复制"),i.destroy()}))})())}," 复制链接 ",8,["data-clipboard-text"])])])],64))}};export default si;
