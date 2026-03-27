<?php if(!defined('APP_NAME')) exit;?>

<div class="sidebar inner">
  <div class="sb_nav">
    <h3 class='title myCorner' data-corner='top 5px'>公司产品</h3>
    <div class="active" id="sidebar" data-csnow="2" data-class3="0" data-jsok="2">
			 {loop $sorts $key $vo}  
      {if strpos($vo['path'],'100024')!==false && $key!='100024'}              
      <dl class="list-none navnow">
        <dt id='part2_7'><a href='{$vo['url']}'  title='{$vo['name']}' class="zm"><span>{$vo['name']}</span></a></dt>
      </dl>{/if}
{/loop}
      <div class="clear"></div>
    </div>
    <h3 class='title line myCorner' data-corner='top 5px'>联系方式</h3>
    <div class="active editor">
      {piece:lianxi}
      <div class="clear"></div>
    </div>
  </div>
  <div class="sb_box">
    <h3 class="title">
      <div class="position">当前位置 {$daohang}</div>
      <span> {$title}</span> </h3>
    <div class="clear"></div>
    <div class="active" id="newslist">
      <ul class='list-none metlist'>
	  {loop $alist $vo}
        <li class='list top'><span>[{date($vo['addtime'],Y-m-d H:m:i)}]</span><a href='{url($vo['method'],array('id'=>$vo['id']))}' title='{$vo['title']}' target='_self'>{$vo['title']}</a>
          <p></p>
        </li>{/loop}
       
      </ul>
      <div id="flip">
        <style>
.digg4  { padding:3px; margin:3px; text-align:center; font-family:Tahoma, Arial, Helvetica, Sans-serif;  font-size: 12px;}.digg4  a { border:1px solid #ddd; padding:2px 5px 2px 5px; margin:2px; color:#aaa; text-decoration:none;}.digg4  a:hover { border:1px solid #a0a0a0; }.digg4  a:hover { border:1px solid #a0a0a0; }.digg4  span.current {border:1px solid #e0e0e0; padding:2px 5px 2px 5px; margin:2px; color:#aaa; background-color:#f0f0f0; text-decoration:none;}.digg4  span.disabled { border:1px solid #f3f3f3; padding:2px 5px 2px 5px; margin:2px; color:#ccc;} 
</style>
        <div class='digg4'><span class='disabled' style='font-family: Tahoma, Verdana;'><b>{$page}</b></span></div>
      </div>
    </div>
  </div>
  <div class="clear"></div>
</div>
