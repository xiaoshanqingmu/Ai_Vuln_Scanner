<?php if(!defined('APP_NAME')) exit;?>
<link rel="stylesheet" href="__PUBLICAPP__/css/jqzoom.css" type="text/css" media="screen">
<script type="text/javascript" src="__PUBLICAPP__/js/jquery.jqzoom.js"></script>
<script type="text/javascript">//幻灯
var t = n = count = 0;   //n为当前序号 t为周期参数 
$(function(){    
    $(".jqzoom").jqueryzoom({
						xzoom: 250,		//zooming div default width(default width value is 200)
						yzoom: 250,		//zooming div default width(default height value is 200)
						offset: 5,		//zooming div default offset(default offset value is 10)
						position: "right",  //zooming div position(default position value is "right")
                        preload: 1     // 1 by default
					});	
    count = $("#play_list .jqzoom").size();    
    $("#play_list .jqzoom:not(:first-child)").hide(); 
    $("#play_menu li:first-child").css({"border":"1px solid #2974c5"});    
    $("#play_menu li").click(function(){    
        var i = $(this).attr('alt') - 1;
        n = i;
        if (i >= count) return;    
        $(this).css({"border":"1px solid #2974c5"}).siblings().css({"border":"0px"});
        $("#play_list .jqzoom").filter(":visible").hide().parent().children().eq(i).fadeIn(500);         
    });    
    t = setInterval("showAuto()", 3000); 
    $("#play").hover(function(){ clearInterval(t) }, function(){t = setInterval("showAuto()", 3000);});  
	
	//购物车ajax提交
	$("#buy").click(function(){
		    var num=parseInt($("#num").val());
	        $.post("{url('member/shopcar/caradd')}", {code:"{$extdata['stand']}",name:"{$info['title']}",price:"{$extdata['price']}",num:num},
   				function(data){
					if(confirm(data+"是否进入会员中心管理购物车?")){
                       window.location.href="{url('member/index/index',array('act'=>url('member/shopcar/index')))}";
                    }else return false;
   			});
    });
})    
function showAuto()
{
    n = n >= (count - 1) ? 0 : n + 1;
    $("#play_menu li").eq(n).trigger('click'); 
}
//购物车ajax提交
function order(code,name,price,num){
			
}
</script>
<div id="Main">
<div class="adv">
    <img src="__PUBLICAPP__/images/banner.png">
</div>
<div class="yx-g">
    <div class="yx-u-17-24">
       <div class="box index-big">
           <div class="bock-tit"><h3>当前位置： {$daohang}</h3></div>
           <div class="yx-g">
              <div class="yx-u-1-2">
                 <div id="play">
                  <div id="play_list">
                    {loop $photolist $vo}
                       <div class="jqzoom"><img alt="{$vo['tit']}" src="{$PhotoImgPath}{$vo['picture']}" jqimg="{$PhotoImgPath}{$vo['picture']}" width="310" height="235" /></div>
                    {/loop} 
                  </div>  
                  <ul id="play_menu">
                    {loop $photolist $vo}
                       <li alt="{$n}"><img src="{$PhotoImgPath}thumb_{$vo['picture']}" width="55" height="42"/></li>
                    {/loop} 
                  </ul>
                 </div>  
              </div>
              
              <div class="yx-u-1-2">
                 <ul class="proinfo">            
                   <li>产品名称：{$info['title']}</li>
                   <li>{$extinfo[2]['name']}：{$extinfo[2]['value']}</li>
                   <li>{$extinfo[3]['name']}：{$extinfo[3]['value']}</li>
                   <li>{$extinfo[4]['name']}：{$extinfo[4]['value']}</li>
                   <li>{$extinfo[5]['name']}：{$extinfo[5]['value']}</li>
                   <li>{$extinfo[6]['name']}：{$extinfo[6]['value']}</li>
                   <li>发布日期：{date($info['addtime'],Y-m-d H:m:i)}</li>
                   <li>数量：<input type="text" value="1" name="num" id="num" size="3" class="ie6input">&nbsp;<button id="buy" class="yx-button">加入购物车</button></li>
                 </ul>
              </div>
           </div>
           <div class="bock-tit"><h2>详细介绍</h2></div>
           <p class="pro-instr">{$info['content']}</p>
           {piece:duoshuo}
       </div>
    </div>
    {include file="prightCom"}
</div>
</div>