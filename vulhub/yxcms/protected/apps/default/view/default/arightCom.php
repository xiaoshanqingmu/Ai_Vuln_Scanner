<?php if(!defined('APP_NAME')) exit;?>
<div class="yx-u-7-24 ">
       {if !empty($sortlist)}  <!--子栏目列表-->
       <div class="block box">
          <div class="bock-tit"><h2>{$title}</h2></div>
          <ul class="bock-list">
            {loop $sortlist $key $vo}  
               <li><a class="w180" title="{$vo['name']}"  href="{$vo['url']}">{$vo['name']}</a></li>
             {/loop}
          </ul>
       </div>
       {/if}
       
       <div class="block box">
          <div class="bock-tit"><h2>公告信息</h2></div>
          <div class="bock-con">{piece:announce}</div>
       </div>
       
       <div class="block box">
          <div class="bock-tit"><h2>热门文章</h2></div>
          <ul class="bock-list">
            {hot:{table=(news) field=(id,title,color,addtime,method) order=(hits desc,id desc) where=(ispass='1') limit=(7)}}
                     <li><a class="w180" style="color:[hot:color]" title="[hot:title]" target="_blank" href="{url($hot['method'],array('id'=>$hot['id']))}">[hot:title $len=25]</a><span>{date($hot['addtime'],Y-m-d)}</span></li>
           {/hot}
          </ul>
       </div>
       
       <div class="block box">
          <div class="bock-tit"><h2>推荐文章</h2></div>
          <ul class="bock-list">
           {recmd:{table=(news) field=(id,title,color,addtime,method) order=(recmd desc,id desc) where=(ispass='1') limit=(7)}}
                     <li><a class="w180" style="color:[recmd:color]" title="[recmd:title]" target="_blank" href="{url($recmd['method'],array('id'=>$recmd['id']))}">[recmd:title $len=25]</a><span>{date($recmd['addtime'],Y-m-d)}</span></li>
           {/recmd}
          </ul>
       </div>
</div> 