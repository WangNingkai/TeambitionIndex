import{c as i,r as t,o as e,a as s,b as a,F as o,u as d,e as l,d as n,t as r,g as c,h as m,i as u,j as p,k as g,m as h}from"./index.8f74753c.js";import{s as y,d as b}from"./share.fe8fbfd4.js";import{_ as v,i as x,C as P}from"./clipboard.623c6b1d.js";const w={class:"mdui-row mdui-shadow-3 mdui-p-a-1 mdui-m-y-3",style:{"border-radius":"8px"}},f={class:"mdui-list"},k=c('<li class="mdui-list-item mdui-ripple"><div class="mdui-row mdui-col-xs-12"><div class="mdui-col-xs-12 mdui-col-sm-6">文件</div><div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">分享时间</div><div class="mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right">操作</div></div></li>',1),C={key:0,class:"mdui-list-item mdui-ripple"},M=a("div",{class:"mdui-col-sm-12"},[a("i",{class:"mdui-icon material-icons"},"info"),m(" 没有更多数据呦")],-1),$={class:"mdui-row mdui-col-sm-12 mdui-valign"},_={class:"mdui-col-xs-12 mdui-col-sm-6 mdui-text-truncate"},j={"data-name":"{{ node.name }}",href:"javascript:void(0);","aria-label":"File"},D=a("i",{class:"mdui-icon material-icons"}," insert_drive_file ",-1),E={class:"mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right"},F={class:"mdui-col-sm-3 mdui-hidden-sm-down mdui-text-right"},R=a("i",{class:"mdui-icon material-icons"},"content_copy",-1),S=a("i",{class:"mdui-icon material-icons"},"delete",-1),q=a("div",{class:"mdui-col-sm-12 mdui-typo-body-1-opacity mdui-text-center"},[m(" 加载更多 "),a("i",{class:"mdui-icon material-icons"},"expand_more")],-1),H={class:"mdui-list-item mdui-ripple"},Y={class:"mdui-col-sm-12 mdui-typo-body-1-opacity"},z={expose:[],setup(c){Date.prototype.Format=function(i){let t={"M+":this.getMonth()+1,"d+":this.getDate(),"h+":this.getHours(),"m+":this.getMinutes(),"s+":this.getSeconds(),"q+":Math.floor((this.getMonth()+3)/3),S:this.getMilliseconds()};/(y+)/.test(i)&&(i=i.replace(RegExp.$1,(this.getFullYear()+"").substr(4-RegExp.$1.length)));for(let e in t)new RegExp("("+e+")").test(i)&&(i=i.replace(RegExp.$1,1==RegExp.$1.length?t[e]:("00"+t[e]).substr((""+t[e]).length)));return i};u(),p();const m=i((()=>window.location.origin)),z=t({list:[],perPage:100,currentPage:1,totalCount:0,totalPage:1,loading:!1}),A=async()=>{z.loading=!0,await y({currentPage:z.currentPage,perPage:z.perPage}).then((i=>{z.loading=!1;const t=i.result;z.list=t.list,z.currentPage=t.currentPage,z.perPage=t.perPage,z.totalCount=t.totalCount,z.totalPage=t.totalPage}))};return e((()=>{A()})),(i,t)=>(g(),s("div",w,[a("ul",f,[k,z.loading?(g(),s(v,{key:0,color:"mdui-color-blue-200"})):(g(),s(o,{key:1},[d(x)(z.list)?(g(),s("li",C,[M])):(g(!0),s(o,{key:1},l(z.list,(i=>(g(),s("li",{key:i.id,class:"mdui-list-item mdui-ripple"},[a("div",$,[a("div",_,[a("a",j,[D,a("span",null," "+r(i.name),1)])]),a("div",E,r(new Date(1e3*i.created_at).Format("yyyy-MM-dd hh:mm:ss")),1),a("div",F,[a("a",{class:"clipboard mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download","aria-label":"Delete","mdui-tooltip":"{content: '复制链接'}","data-clipboard-text":`${d(m)}/s/${i.hash}`,onClick:t[1]||(t[1]=i=>(()=>{const i=new P(".clipboard");i.on("success",(t=>{h.snackbar({message:":) 复制成功",timeout:2e3,position:"right-top"}),i.destroy()})),i.on("error",(t=>{console.error("该浏览器不支持自动复制"),i.destroy()}))})())},[R],8,["data-clipboard-text"]),a("a",{class:"mdui-btn mdui-ripple mdui-btn-icon mdui-hidden-sm-down download","aria-label":"Delete","mdui-tooltip":"{content: '删除'}",onClick:t=>{return e=i.id,void h.confirm("确定删除吗",(function(){b(e).then((i=>{const t=i.result;console.log(t),h.snackbar({message:":) 删除成功",timeout:2e3,position:"right-top"}),A()}))}));var e}},[S],8,["onClick"])])])])))),128)),z.currentPage<z.totalPage?(g(),s("li",{key:2,onClick:t[2]||(t[2]=i=>(async()=>{await y({currentPage:z.currentPage,perPage:z.perPage}).then((i=>{const t=i.result;z.currentPage=t.currentPage,z.list=z.list.concat(t.list)}))})()),class:"mdui-list-item mdui-ripple"},[q])):n("v-if",!0),a("li",H,[a("div",Y,r(z.totalCount)+" 个项目 ",1)])],64))])]))}};export default z;