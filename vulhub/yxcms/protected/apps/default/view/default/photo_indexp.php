<?php if(!defined('APP_NAME')) exit;?>
<div id="Main">
<div class="adv">
    <img src="__PUBLICAPP__/images/banner.png">
</div>
<div class="yx-g">
    <div class="yx-u-17-24">
       <div class="box index-big">
           <div class="bock-tit"><h3>当前位置： {$daohang}</h3></div>
            <ul class="prlist yx-u">
               {loop $plist $vo}
                  <li><a href="{url($vo['method'],array('id'=>$vo['id']))}" target="_blank"><img class="box" src="{$PhotoImgPath}thumb_{$vo['picture']}" width="145" height="110"></a><h2>{$vo['title']}</h2><span>{date($vo['addtime'],Y-m-d H:m:i)}</span></li>
              {/loop}   
            </ul>
            <div class="pagelist yx-u">{$page}</div> 
       </div>
    </div>
    {include file="prightCom"}
</div>
</div>